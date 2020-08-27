@foreach( $data as $collection )  
    @php
        $image = $collection->collection_images()->take(1)->first();

        if( $collection->collection_images()->count() != 0 ) {
            $imageCollection = $image->images()->thumbnail;
        } else {
            $imageCollection = 'img-collection.jpg';
        }
        $s3Url = Storage::disk('s3')->url(config('path.thumbnail').$imageCollection);
    @endphp

    <!-- Start Item -->
    <a
        href="{{ url($collection->user()->username.'/collection', $collection->id) }}"
        data-w="{{App\Helper::getWidth($s3Url)}}"
        data-h="{{App\Helper::getHeight($s3Url)}}"
        {{-- data-w="{{App\Helper::getWidth('public/uploads/thumbnail/'.$imageCollection)}}" --}}
        {{-- data-h="{{App\Helper::getHeight('public/uploads/thumbnail/'.$imageCollection)}}" --}}
        class="item hovercard">

        <!-- hover-content -->
        <span class="hover-content">
            <h5
                class="text-overflow title-hover-content"
                title="{{$collection->title}}">
                @if($collection->type == 'private')
                    <span class="label label-default">
                        {{trans('misc.private')}}
                    </span>
                @endif
                {{$collection->title}}
            </h5>
            
            <h5
                class="text-overflow author-label mg-bottom-xs"
                title="{{$collection->user()->username}}">
                <img
                    loading="lazy"
                    @if($collection->user()->avatar == 'default.jpg')
                        src={{ url('public/avatar', 'default.jpg') }}
                    @else
                        src="{{ App\Helper::imgixUrl('path.avatar', $collection->user()->avatar) }}"
                    @endif
                    alt="User"
                    class="img-circle profile-img"> 

                <em>{{$collection->user()->username}}</em>
            </h5>

            <span
                class="timeAgo btn-block date-color text-overflow"
                data="{{ date('c', strtotime( $collection->created_at )) }}">
            </span>
            
            <span class="sub-hover">
                <span class="myicon-right">
                    <i class="icon icon-Picture myicon-right"></i>
                    {{$collection->collection_images()->count()}}
                </span>
            </span><!-- Span Out -->
        </span><!-- hover-content -->
        
        <img src="{{ App\Helper::imgixUrl('path.thumbnail', $imageCollection) }}" />
    </a><!-- End Item -->
@endforeach