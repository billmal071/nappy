@foreach( $images as $image )

	@php
		$stockImages = $image->stock;
	@endphp
<!-- Start Item -->
<a href="{{ url('photo', $image->id ) }}/{{str_slug($image->title)}}" class="item hovercard" data-w="{{App\Helper::getWidth('public/uploads/small/'.$stockImages{0}->name)}}" data-h="{{App\Helper::getHeight('public/uploads/small/'.$stockImages{0}->name)}}">
	<!-- hover-content -->
	<span class="hover-content">
			<h5 class="text-overflow title-hover-content" title="{{$image->title}}">
				@if( $image->featured == 'yes' ) <i class="icon icon-Medal myicon-right" title="{{trans('misc.featured')}}"></i> @endif {{$image->title}}
				</h5>

			<h5 class="text-overflow author-label mg-bottom-xs" title="{{$image->user()->username}}">
				<img src="{{ url('public/avatar/',$image->user()->avatar) }}" alt="User" class="img-circle" style="width: 20px; height: 20px; display: inline-block; margin-right: 5px;">
				<em>{{$image->user()->username}}</em>
				</h5>
				<span class="timeAgo btn-block date-color text-overflow" data="{{ date('c', strtotime( $image->date )) }}"></span>

			<span class="sub-hover">
				@if($image->item_for_sale == 'sale')
				<span class="myicon-right"><i class="fa fa-shopping-cart myicon-right"></i> {{\App\Helper::amountFormat($image->price)}}</span>
			@endif
				<span class="myicon-right"><i class="fa fa-heart-o myicon-right"></i> {{$image->likes()->count()}}</span>
				<span class="myicon-right"><i class="icon icon-Download myicon-right"></i> {{$image->downloads()->count()}}</span>
			</span><!-- Span Out -->
	</span><!-- hover-content -->

		<img src="{{ url('public/uploads/small/',$stockImages{0}->name) }}" />
</a><!-- End Item -->

@endforeach


@if( $images->count() != 0  )
			    <div class="container-paginator">
			    	{{ $images->links() }}
			    	</div>
			    	@endif
