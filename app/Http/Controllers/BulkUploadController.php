<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\AdminSettings;
use App\Models\Images;
use App\Models\Stock;
use App\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use Image;


class BulkUploadController extends Controller {

    public function __construct(AdminSettings $settings) {
        $this->settings = $settings::first();
    }
    // START
    public function bulkUpload()
    {
        return view('admin.bulk-upload');

    }//<--- END METHOD

    public function bulkUploadStore(Request $request)
    {

        // PATHS
        $temp            = config('path.uploads');
        $path_preview    = config('path.preview');
        $path_thumbnail  = config('path.thumbnail');
        $path_small      = config('path.small');
        $path_medium     = config('path.medium');
        $path_large      = config('path.large');
        // $temp            = 'public/temp/';
        // $path_preview    = 'public/uploads/preview/';
        // $path_thumbnail  = 'public/uploads/thumbnail/';
        // $path_small      = 'public/uploads/small/';
        // $path_medium     = 'public/uploads/medium/';
        // $path_large      = 'public/uploads/large/';
        // $watermarkSource = 'public/img/watermark.png';

        $input          = $request->all();
        $countPhotos    = count($request->file('photo'));
        $maxUploadFiles = ini_get("max_file_uploads");

        if($countPhotos > $maxUploadFiles) {
            return back()
                ->with('error_max_upload',
                        trans('bulk_upload.max_files_upload_same_time',
                        ['max_file_uploads' => ini_get("max_file_uploads")]
                    ))
                ->withInput();
        }

        $dimensions = explode('x',$this->settings->min_width_height_image);

        if($this->settings->currency_position == 'right') {
            $currencyPosition =  2;
        } else {
            $currencyPosition =  null;
        }

        $messages = [
            'photo.*.dimensions' => trans('bulk_upload.photo_dimensions'),
            "mimes.*.dimensions"   => trans('bulk_upload.photo_mimes'),
            "price.required_if" => trans('misc.price_required'),

            'price.min' => trans('misc.price_minimum_sale'.$currencyPosition, [
                'symbol' => $this->settings->currency_symbol,
                'code' => $this->settings->currency_code ]),

            'price.max' => trans('misc.price_maximum_sale'.$currencyPosition, [
                'symbol' => $this->settings->currency_symbol,
                'code' => $this->settings->currency_code]),
        ];

        $validator = Validator::make($request->all(), [
            'tags' => 'required',
            'photo' => 'required',
            'price' => 'required_if:item_for_sale,==,sale|integer|min:'.$this->settings->min_sale_amount.'|max:'.$this->settings->max_sale_amount.'',
            'photo.*' => 'mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width='.$dimensions[0].',min_height='.$dimensions[1].'|max:'.$this->settings->file_size_allowed.''
        ],$messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if($request->hasfile('photo')) {

            foreach($request->file('photo') as $key => $file) {
                try {
                    $this->validateImageUploaded($file->getPathName());
                } catch (\Exception $e) {
                    return back()
                        ->with('error_max_upload', trans('bulk_upload.error_image_uploaded').' ('.$file->getClientOriginalName().')' )
                        ->withInput();
                        exit;
                }

                $replace = ['+','-','_','.'];

                $extension       = $file->getClientOriginalExtension();
                $fileOriginalName = Helper::fileNameOriginal($file->getClientOriginalName());
                $originalNameReplace = strlen($fileOriginalName) > 50 ? substr($fileOriginalName, -25) : $fileOriginalName;
                $originalName    = str_replace( $replace, ' ', $originalNameReplace);
                $type_mime_img   = $file->getMimeType();
                $sizeFile        = $file->getSize();
                $large           = strtolower(str_slug($originalName, '-').'-'.Auth::user()->id.time().str_random(100).'.'.$extension );
                $medium          = strtolower(str_slug($originalName, '-').'-'.Auth::user()->id.time().str_random(100).'.'.$extension );
                $small           = strtolower(str_slug($originalName, '-').'-'.Auth::user()->id.time().str_random(100).'.'.$extension );
                $preview         = strtolower(str_slug($originalName, '-').'-'.Auth::user()->id.time().str_random(10).'.'.$extension );
                $thumbnail       = strtolower(str_slug($originalName, '-').'-'.Auth::user()->id.time().str_random(10).'.'.$extension );


                if($file->move($temp, $large)) {
                    set_time_limit(0);

                    $original = $temp.$large;
                    $width    = Helper::getWidth( $original );
                    $height   = Helper::getHeight( $original );

                    if ($width > $height) {
                        if($width > 1280) : $_scale = 1280; else: $_scale = 900; endif;

                        // PREVIEW
                        $scale    = 850 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scale, $temp.$preview );

                        // Medium
                        $scaleM   = $_scale / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleM, $temp.$medium );

                        // Small
                        $scaleS   = 640 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleS, $temp.$small );

                        // Thumbnail
                        $scaleT   = 280 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleT, $temp.$thumbnail );
                    } else {
                        if($width > 1280) : $_scale = 960; else: $_scale = 800; endif;

                        // PREVIEW
                        $scale    = 480 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scale, $temp.$preview );

                        // Medium
                        $scaleM   = $_scale / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleM, $temp.$medium );

                        // Small
                        $scaleS   = 480 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleS, $temp.$small );

                        // Thumbnail
                        $scaleT   = 190 / $width;
                        $uploaded = Helper::resizeImage( $original, $width, $height, $scaleT, $temp.$thumbnail );

                    }

                    // Helper::watermark($temp.$preview, $watermarkSource);

                }// End $file -> move

                // Exif Read Data
                $exif_data = @exif_read_data($temp.$large, 0, true);
                $exif = $this->exifData($temp.$large);

                if( isset($exif_data['IFD0']['Model']) ) {
                    $camera = $exif_data['IFD0']['Model'];
                } else {
                    $camera = '';
                }

                $colors_image = $this->colorExtractorPhoto($temp.$preview);

                $token_id = str_random(200);

                $sql = new Images;
                $sql->thumbnail            = $thumbnail;
                $sql->preview              = $preview;
                $sql->title                = $originalName;
                $sql->description          = '';
                $sql->categories_id        = $request->categories_id;
                $sql->user_id              = Auth::user()->id;
                $sql->status               = 'active';
                $sql->token_id             = $token_id;
                $sql->tags                 = $request->tags;
                $sql->extension            = strtolower($extension);
                $sql->colors               = $colors_image;
                $sql->exif                 = trim($exif);
                $sql->camera               = $camera;
                $sql->how_use_image        = $request->how_use_image;
                $sql->attribution_required = $request->attribution_required;
                $sql->original_name        = $originalName;
                $sql->price                = $request->price;
                $sql->item_for_sale        = $request->item_for_sale;
                $sql->save();

                // ID INSERT
                $imageID = $sql->id;

                // INSERT STOCK IMAGES

                $lResolution = list($w, $h) = getimagesize($temp.$large);
                $lSize     = Helper::formatBytes(filesize($temp.$large), 1);

                $mResolution = list($_w, $_h) = getimagesize($temp.$medium);
                $mSize     = Helper::formatBytes(filesize($temp.$medium), 1);

                $smallResolution = list($__w, $__h) = getimagesize($temp.$small);
                $smallSize       = Helper::formatBytes(filesize($temp.$small), 1);

                $stockImages = [
                    ['name' => $large, 'type' => 'large', 'resolution' => $w.'x'.$h, 'size' => $lSize ],
                    ['name' => $medium, 'type' => 'medium', 'resolution' => $_w.'x'.$_h, 'size' => $mSize ],
                    ['name' => $small, 'type' => 'small', 'resolution' => $__w.'x'.$__h, 'size' => $smallSize ],
                ];

                foreach ($stockImages as $key) {
                    $stock             = new Stock;
                    $stock->images_id  = $imageID;
                    $stock->name       = $key['name'];
                    $stock->type       = $key['type'];
                    $stock->extension  = $extension;
                    $stock->resolution = $key['resolution'];
                    $stock->size       = $key['size'];
                    $stock->token      = $token_id;
                    $stock->save();
                }

                Storage::disk('s3')
                    ->put($path_preview.$preview, file_get_contents($temp.$preview), 'public');
                \File::delete($temp.$preview);

                Storage::disk('s3')
                    ->put($path_thumbnail.$thumbnail, file_get_contents($temp.$thumbnail), 'public');
                \File::delete($temp.$thumbnail);

                Storage::disk('s3')
                    ->put($path_small.$small, file_get_contents($temp.$small), 'public');
                \File::delete($temp.$small);

                Storage::disk('s3')
                    ->put($path_medium.$medium, file_get_contents($temp.$medium), 'public');
                \File::delete($temp.$medium );

                Storage::disk('s3')
                    ->put($path_large.$large, file_get_contents($temp.$large), 'public');
                \File::delete($temp.$large);

                // \File::copy($temp.$preview, $path_preview.$preview);
                // \File::delete($temp.$preview);

                // \File::copy($temp.$thumbnail, $path_thumbnail.$thumbnail);
                // \File::delete($temp.$thumbnail);

                // \File::copy($temp.$small, $path_small.$small);
                // \File::delete($temp.$small);

                // \File::copy($temp.$medium, $path_medium.$medium );
                // \File::delete($temp.$medium );

                // \File::copy($temp.$large, $path_large.$large);
                // \File::delete($temp.$large);

            }// Foreach

        }// HasFile -> Photo

        return back()
            ->with('success_upload', "($countPhotos) ".trans_choice('bulk_upload.success_upload_photos',$countPhotos))
            ->withInput();

    }// End Method bulkUploadStore()

    protected function validateImageUploaded($image) {

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($image),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            ),
        true)) {
        throw new \Exception('Invalid file format.');
        }
    }// End method

    protected function colorExtractorPhoto($image)
    {
        //=========== Colors
        $palette = Palette::fromFilename(url($image));

        $extractor = new ColorExtractor($palette);

        // it defines an extract method which return the most “representative” colors
        $colors = $extractor->extract(5);
        $total = $colors;

        // $palette is an iterator on colors sorted by pixel count
        foreach($colors as $color) {
            $_color[] = trim(Color::fromIntToHex($color), '#');
        }

         return $colors_image = implode( ',', $_color);
    }// End Method colorExtractorPhoto

    protected function exifData($image)
    {
        $exif_data = @exif_read_data($image, 0, true);

        if( isset($exif_data['EXIF']['ISOSpeedRatings'][0]) ) {
            $ISO = 'ISO '.$exif_data['EXIF']['ISOSpeedRatings'][0];
        }

        if( isset($exif_data['EXIF']['ExposureTime']) ) {
            $ExposureTime = $exif_data['EXIF']['ExposureTime'].'s';
        }

        if( isset($exif_data['EXIF']['FocalLength']) ) {
            $FocalLength = round($exif_data['EXIF']['FocalLength'], 1).'mm';
        }

        if( isset($exif_data['COMPUTED']['ApertureFNumber']) ) {
            $ApertureFNumber = $exif_data['COMPUTED']['ApertureFNumber'];
        }

        if( !isset($FocalLength) ) {
            $FocalLength = '';
        }

        if( !isset($ExposureTime) ) {
            $ExposureTime = '';
        }

        if( !isset($ISO) ) {
            $ISO = '';
        }

        if( !isset($ApertureFNumber) ) {
            $ApertureFNumber = '';
        }

        return $FocalLength.' '.$ApertureFNumber.' '.$ExposureTime. ' '.$ISO;

    }// End method

}
