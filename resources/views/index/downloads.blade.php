@extends('app')

@section('title'){{ trans('misc.most_downloads').' - ' }}@endsection

@section('content')

@include('includes.nav-pills')

<div class="container-fluid margin-bottom-40 padding-top-40">
	<div class="row">

		<!-- col-md-8 -->
       <div class="col-md-12">

            <div id="gallery-div" class="row margin-bottom-20 gallery-container">
                @include('includes.gallery')
            </div>

		</div><!-- col-md-12-->

	</div><!-- row -->
</div><!-- container -->
@endsection

@section('javascript')

	<script src="{{ asset('public/js/loadingoverlay.js') }}"></script>
    <script src="{{ asset('public/js/custom/gallery.js') }}"></script>

<script type="text/javascript">

 $('#imagesFlex').flexImages({ rowHeight: 320 });

//<<---- PAGINATION AJAX
        $(document).on('click','.pagination a', function(e){
			e.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			$.ajax({
				headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
					url: '{{ url("/") }}/ajax/downloads?page=' + page


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
