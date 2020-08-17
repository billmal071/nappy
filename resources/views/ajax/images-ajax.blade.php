@foreach( $images as $image )

@php
    $stockImages = $image->stock;
    $colors = explode(",", $image->colors);
    $color = $colors[0];

    // Width and Height Large
    $imageLarge = App\Models\Stock::whereImagesId($image->id)
                    ->whereType('large')
                    ->pluck('resolution')
                    ->first();

    if($image->extension == 'png' ) {
        // $background = 'background: url('.url('public/img/pixel.gif').') repeat center center #e4e4e4;';
        $background = 'background: url('.App\Helper::getUrlFromS3('path.img', 'pixel.gif').') repeat center center #e4e4e4;';
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
        $thumbnail = App\Helper::getUrlFromS3('path.small', $stockImage->name);
    }
@endphp
<!-- Start Item -->
<a  href="{{ url('photo', $image->id ) }}/{{str_slug($image->title)}}"
    class="item hovercard"
    data-w="{{ $newWidth }}"
    data-h="{{ $newHeight }}"
>
    <span class="hover-content">
        <h5 class="text-overflow title-hover-content" title="{{$image->title}}">
            @if( $image->featured == 'yes' )
                <i class="icon icon-Medal myicon-right" title="{{trans('misc.featured')}}"></i>
            @endif
            {{$image->title}}
        </h5>

        <h5 class="text-overflow author-label mg-bottom-xs" title="{{$image->user()->username}}">
            <img
                {{-- src="{{ url('public/avatar/',$image->user()->avatar) }}" --}}
                @if($image->user()->avatar == 'default.jpg')
                    src={{ url('public/avatar', 'default.jpg') }}
                @endif
                src="{{ App\Helper::getUrlFromS3('path.avatar', $image->user()->avatar) }}"
                alt="User"
                class="img-circle"
                style="width: 20px; height: 20px; display: inline-block; margin-right: 5px;">
            <em>{{$image->user()->username}}</em>
        </h5>
        <span
            class="timeAgo btn-block date-color text-overflow"
            data="{{ date('c', strtotime( $image->date )) }}">
        </span>

      <span class="sub-hover">
            @if($image->item_for_sale == 'sale')
                <span class="myicon-right">
                    <i class="fa fa-shopping-cart myicon-right"></i>
                    {{\App\Helper::amountFormat($image->price)}}
                </span>
            @endif
            <span class="myicon-right">
                <i class="fa fa-heart-o myicon-right"></i>
                {{$image->likes()->count()}}
            </span>
            <span class="myicon-right">
                <i class="icon icon-Download myicon-right"></i>
                {{$image->downloads()->count()}}
            </span>
        </span><!-- Span Out -->
    </span><!-- hover-content -->

    {{-- <img src="{{ url('public/uploads/small/',$stockImages{0}->name) }}" /> --}}
    <img src="{{ App\Helper::getUrlFromS3('path.small', $stockImages{0}->name) }}" />
</a><!-- End Item -->
@endforeach

@if( $images->count() != 0  )
    <div class="container-paginator">
        {{ $images->links() }}
    </div>
@endif
