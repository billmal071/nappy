@extends('app')

@section('title'){{ $category->name.' - ' }}@endsection

@section('content')
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h1 class="title-site title-sm">{{ $category->name }}</h1>


       @if( $images->total() != 0 )
        	<p class="subtitle-site"><strong>({{number_format($images->total())}}) {{trans_choice('misc.images_available_category',$images->total() )}}</strong></p>
        @else
        	<p class="subtitle-site"><strong>{{$settings->title}}</strong></p>
        @endif
      </div>
    </div>

<div class="container-fluid margin-bottom-40">

<!-- Col MD -->
<div class="col-md-12 margin-top-20 margin-bottom-20">

    <div id="gallery-div" class="row margin-bottom-20 gallery-container">
        @include('includes.gallery')
    </div>

 </div><!-- /COL MD -->

 </div><!-- container wrap-ui -->

@endsection

@section('javascript')

    <script src="{{ asset('public/js/loadingoverlay.js') }}"></script>
    <script src="{{ asset('public/js/custom/gallery.js') }}"></script>

<script type="text/javascript">

 $('#imagesFlex').flexImages({ rowHeight: 320, truncate: false });

//<<---- PAGINATION AJAX
        $(document).on('click','.pagination a', function(e){
			e.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			$.ajax({
				headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
					url: '{{ url("/") }}/ajax/category?slug={{$category->slug}}&page=' + page


			}).done(function(data){
				if( data ) {

					scrollElement('#imagesFlex');

					$('.dataResult').html(data);

					$('.hovercard').hover(
		               function () {
		                  $(this).find('.hover-content').fadeIn();
		               },
		               function () {
		                  $(this).find('.hover-content').fadeOut();
		               }
		            );

					$('#imagesFlex').flexImages({ rowHeight: 320 });
					jQuery(".timeAgo").timeago();

					$('[data-toggle="tooltip"]').tooltip();
				} else {
					sweetAlert("{{trans('misc.error_oops')}}", "{{trans('misc.error')}}", "error");
				}
				//<**** - Tooltip
			});

		});//<<---- PAGINATION AJAX

</script>


@endsection
