@foreach( $images as $image )

@php
    $stockImages = $image->stock;
    $imgHref = url('photo')."/".$image->id;
@endphp
<!-- Start Item -->
<a href="{{$imgHref}}" class="item hovercard image-btn" id="{{ $image->id }}" data-img-id="{{ $image->id }}" data-w="{{App\Helper::getWidth('public/uploads/small/'.$stockImages{0}->name)}}" data-h="{{App\Helper::getHeight('public/uploads/small/'.$stockImages{0}->name)}}" style="cursor: pointer;">
    <!-- hover-content -->
    <span class="hover-content">
        <span class="sub-hover">
            <span class="myicon-right" style="float: left;">
                <img src="{{ url('public/avatar/',$image->user()->avatar) }}" alt="User" class="img-circle profile-img">
                <em>{{$image->user()->username}}</em>
            </span>
            <span class="myicon-right thumbnail-like"><i class="fa fa-heart-o myicon-right"></i> {{$image->likes()->count()}}</span>
            <span class="myicon-right thumbnail-download"><i class="icon icon-Download myicon-right"></i> {{$image->downloads()->count()}}</span>
        </span><!-- Span Out -->
    </span><!-- hover-content -->

        <img src="{{ url('public/uploads/small',$stockImages{0}->name) }}" class="previewImage" />
</a><!-- End Item -->
@endforeach

{{--{!! $images->render() !!}--}}