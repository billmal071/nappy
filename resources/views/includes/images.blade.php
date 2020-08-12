@php
    $imageCollection = 'img-collection.jpg';
@endphp

@foreach( $images as $image )

@php
    $stockImages = $image->stock;
    if (empty($stockImages)) {
        $stockImages = \App\Models\Stock::where('images_id', $image->id)->get();
    }
@endphp
<!-- Start Item -->
{{-- @if(!empty($stockImages{0}->name) && App\Helper::getSize(Storage::disk('s3')->url(config('path.small').$stockImages{0}->name)) > 0) --}}
@if(!empty($stockImages{0}->name))
<a 
    class="item hovercard image-btn" 
    id="{{ $image->id }}" 
    data-img-id="{{ $image->id }}"
    data-w="{{App\Helper::getWidth(Storage::disk('s3')->url(config('path.small').$stockImages{0}->name))}}"
    data-h="{{App\Helper::getHeight(Storage::disk('s3')->url(config('path.small').$stockImages{0}->name))}}"
    
>
    <!-- hover-content -->
    <span class="hover-content">
        <span class="sub-hover">
            <span class="myicon-right thumbnail-usr">
                <img src="{{ url('public/avatar/',$image->user()->avatar) }}" alt="User" class="img-circle profile-img">
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
        @if(!empty($stockImages{0}->name))
            src="{{ Storage::disk('s3')->url(config('path.small').$stockImages{0}->name) }}"
        @else
            src="{{ Storage::disk('s3')->url(config('path.thumbnail').$imageCollection) }}"
        @endif
        class="previewImage"
    />
</a><!-- End Item -->
@endif
@endforeach

{{--{!! $images->render() !!}--}}