@foreach( $images as $image )

@php
    $stockImages = $image->stock;
    $imgHref = url('photo')."/".$image->id;

    $colors = explode(",", $image->colors);
    $color = $colors[0];

    // Width and Height Large
    $imageLarge = App\Models\Stock::whereImagesId($image->id)
                    ->whereType('large')
                    ->pluck('resolution')
                    ->first();

    if($image->extension == 'png' ) {
        // $background = 'background: url('.url('public/img/pixel.gif').') repeat center center #e4e4e4;';
        $background = 'background: url('.App\Helper::imgixUrl('path.img', 'pixel.gif').') repeat center center #e4e4e4;';
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

        // $thumbnail = Storage::disk('s3')->url(config('path.small').$stockImage->name);
        $thumbnail = App\Helper::imgixUrl('path.small', $stockImage->name);
    }
@endphp
<!-- Start Item -->
<a
    href="{{$imgHref}}"
    class="item hovercard image-btn"
    id="{{ $image->id }}"
    data-img-id="{{ $image->id }}"
    data-w="{{$newWidth}}"
    data-h="{{$newHeight}}"
    style="cursor: pointer; margin: 0 .5rem;">
    <!-- hover-content -->
    <span class="hover-content">
        <span class="sub-hover">
            <span class="myicon-right" style="float: left;">
                <img
                    loading="lazy"
                    @if ($image->user()->avatar == 'default.jpg')
                        src="{{ url('public/avatar', 'default.jpg') }}"
                    @else
                        src="{{App\Helper::imgixUrl('path.avatar', $image->user()->avatar)}}"
                    @endif
                    alt="User"
                    class="img-circle profile-img">
                <em>{{$image->user()->username}}</em>
            </span>
            <span class="myicon-right thumbnail-like">
                <i class="fa fa-heart-o myicon-right"></i>
                {{$image->likes()->count()}}
            </span>
            <span class="myicon-right thumbnail-download">
                <i class="icon icon-Download myicon-right"></i>
                {{$image->downloads()->count()}}
            </span>
        </span><!-- Span Out -->
    </span><!-- hover-content -->

    <img
        loading="lazy"
        src="{{App\Helper::imgixUrl('path.small', $stockImages{0}->name)}}" 
        class="previewImage" />
</a><!-- End Item -->
@endforeach

{{--{!! $images->render() !!}--}}