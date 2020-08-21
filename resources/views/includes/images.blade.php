@php
    $imageCollection = 'img-collection.jpg';
@endphp

@foreach( $images as $image )
    @php
        $stockImages = $image->stock;
        if (empty($stockImages)) {
            $stockImages = \App\Models\Stock::where('images_id', $image->id)->get();
        }

        $colors = explode(",", $image->colors);
        $color = $colors[0];

        // Width and Height Large
        $imageLarge = App\Models\Stock::whereImagesId($image->id)
                        ->whereType('large')
                        ->pluck('resolution')
                        ->first();

        if($image->extension == 'png' ) {
            // $background = 'background: url('.url('public/img/pixel.gif').') repeat center center #e4e4e4;';
            $backgroun = 'background: url'.App\Helper::imgixUrl('path.img', 'pixel.gif').') repeat center center #e4e4e4;';
        }  else {
            $background = 'background-color: #'.$color.'';
        }

        if($settings->show_watermark == '1') {
            $thumbnail = Storage::url(config('path.preview').$image->preview);

            $resolution = explode('x', App\Helper::resolutionPreview($imageLarge));
            $newWidth = $resolution[0];
            $newHeight = $resolution[1];

        } else {
            $stockImage = App\Models\Stock::whereImagesId($image->id)
                            ->whereType('small')
                            ->first();

            $resolution = explode('x', $stockImage->resolution);
            $newWidth = $resolution[0];
            $newHeight = $resolution[1];

            // $thumbnail = App\Helper::getUrlFromS3('path.small', $stockImage->name);
            $thumbnail = App\Helper::imgixUrl('path.small', $stockImage->name);
        }

    @endphp
    <!-- Start Item -->
    {{-- @if(!empty($stockImages{0}->name) && App\Helper::getSize(Storage::disk('s3')->url(config('path.small').$stockImages{0}->name)) > 0) --}}
    @if(!empty($stockImages{0}->name))
        <a 
            id="{{ $image->id }}" 
            data-img-id="{{ $image->id }}"
            data-w="{{$newWidth}}"
            data-h="{{$newHeight}}"
            class="item hovercard image-btn">
            <!-- hover-content -->
            <span class="hover-content">
                <span class="sub-hover">
                    <span class="myicon-right thumbnail-usr">
                        {{--
                        <img
                            src="{{ url('public/avatar/',$image->user()->avatar) }}"
                            alt="User"
                            class="img-circle profile-img">
                        --}}
                        <img
                            loading="lazy"
                            @if ($image->user()->avatar == 'default.jpg')
                                src="{{ url('public/avatar', 'default.jpg') }}"
                            @else
                                src="{{App\Helper::imgixUrl('path.avatar', $image->user()->avatar)}}"
                            @endif
                            {{-- src="{{App\Helper::getUrlFromS3('path.avatar', $image->user()->avatar)}}" --}}
                            alt="User"
                            class="img-circle profile-img">
                        <em>{{$image->user()->username}}</em>
                    </span>
                    <span class="myicon-right thumbnail-like">
                        <i class="fa fa-heart-o myicon-right"></i> 
                        <span class="like_{{ $image->id }}">{{$image->likes()->count()}}</span>
                    </span>
                    <span class="myicon-right thumbnail-download">
                        <i class="icon icon-Download myicon-right"></i> 
                        <span class="download_{{ $image->id }}">{{$image->downloads()->count()}}</span>
                    </span>
                </span><!-- Span Out -->
            </span><!-- hover-content -->

            @if($image->sponsored == 'yes')
                <div class="spon-back">
                    <span class="spon-txt">SPON</span>
                </div>
            @endif
            
            <img
                loading="lazy"
                @if(!empty($stockImages{0}->name))
                    {{-- src="{{App\Helper::getUrlFromS3('path.small', $stockImages{0}->name)}}" --}}
                    src="{{App\Helper::imgixUrl('path.small', $stockImages{0}->name)}}"
                @else
                    src="{{App\Helper::imgixUrl('path.thumbnail', $imageCollection)}}"
                @endif
                class="previewImage"/>
        </a><!-- End Item -->
    @endif
@endforeach