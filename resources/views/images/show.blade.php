@extends('app')

@section('title'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }}@endsection

@section('description_custom'){{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id.' - ' }} @if( $response->description != '' ){{ App\Helper::removeLineBreak( e( $response->description ) ).' - ' }}@endif @endsection

@section('keywords_custom'){{$response->tags .','}}@endsection

@php
	// returns filename of a photo
	// dd($response->preview);
@endphp

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<meta property="og:type" content="website" />
<meta property="og:image:width" content="{{App\Helper::getWidth(App\Helper::imgixUrl('path.preview', $response->preview))}}"/>
<meta property="og:image:height" content="{{App\Helper::getHeight(App\Helper::imgixUrl('path.preview', $response->preview))}}"/>
<meta property="og:site_name" content="{{$settings->title}}"/>
<meta property="og:url" content="{{url("photo/$response->id").'/'.str_slug($response->title)}}"/>
<meta property="og:image" content="{{App\Helper::imgixUrl('path.preview', $response->preview)}}"/>
<meta property="og:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}"/>
<meta property="og:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}"/>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{App\Helper::imgixUrl('path.preview', $response->preview)}}" />
<meta name="twitter:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}" />
<meta name="twitter:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}"/>
@endsection

@section('content')
<div class="container-fluid margin-bottom-40 margin-top-40">
	@include('includes.image-details')
</div>
@endsection

@section('javascript')
<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

$('.rel-flex-img').flexImages({ maxRows: 1, truncate: false });

$(document).on('click', '#rediToLog', 
    function () {
        location.href = "{{url('login')}}";
   }
);

$('input').iCheck({
          radioClass: 'iradio_flat-green',
          checkboxClass: 'icheckbox_square-green',
        });

@if (session('noty_error'))
    		swal({
    			title: "{{ trans('misc.error_oops') }}",
    			text: "{{ trans('misc.already_sent_report') }}",
    			type: "error",
    			confirmButtonText: "{{ trans('users.ok') }}"
    			});
   		 @endif

   		 @if (session('noty_success'))
    		swal({
    			title: "{{ trans('misc.thanks') }}",
    			text: "{{ trans('misc.send_success') }}",
    			type: "success",
    			confirmButtonText: "{{ trans('users.ok') }}"
    			});
   		 @endif

  @if( Auth::check() )

  $("#reportPhoto").click(function(e) {
  	var element     = $(this);
	e.preventDefault();
  	 element.attr({'disabled' : 'true'});

  	 $('#formReport').submit();

  });

  // Comments Delete
$(document).on('click','.deleteComment',function () {

	var $id = $(this).data('id');

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

		swal(
			{   title: "{{trans('misc.delete_confirm')}}",
			  type: "warning",
			  showLoaderOnConfirm: true,
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			   confirmButtonText: "{{trans('misc.yes_confirm')}}",
			   cancelButtonText: "{{trans('misc.cancel_confirm')}}",
			    closeOnConfirm: false,
			    },
			    function(isConfirm){
			    	 if (isConfirm) {

			element = $(this);

			element.removeClass('deleteComment');

			$.post("{{url('comment/delete')}}",
			{ comment_id: $id },
			function(data){
				if(data.success == true ){
					window.location.reload();
				} else {
					//bootbox.alert(data.error);
					//window.location.reload();
				}

			},'json');

			   }
	       });
		});

  // Likes Comments
		$(document).on('click','.likeComment',function () {

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
			});

			element  = $(this);

			element.html('<i class="fa fa-spinner fa-spin"></i>');

			$.post("{{url('comment/like')}}",
			{ comment_id: $(this).data('id')
			}, function(data){
				if(data.success == true ){
					if( data.type == 'like' ) {
						element.html('<i class="fa fa-heart myicon-right"></i>');
						element.parent('.btn-block').find('.count').html(data.count).fadeIn();
						element.parent('.btn-block').find('.like-small').fadeIn();
						element.blur();

					} else if( data.type == 'unlike' ) {
						element.html('<i class="fa fa-heart-o myicon-right"></i>');

					if( data.count == 0 ) {
						element.parent('.btn-block').find('.count').html(data.count).fadeOut();
						element.parent('.btn-block').find('.like-small').fadeOut();
					} else {
						element.parent('.btn-block').find('.count').html(data.count).fadeIn();
						element.parent('.btn-block').find('.like-small').fadeIn();
					}

						element.blur();
					}
				} else {
					bootbox.alert(data.error);
					window.location.reload();
				}

				if( data.session_null ) {
					window.location.reload();
				}
			},'json');
		});
  @endif

  //<<<---------- Comments Likes
$(document).on('click','.comments-likes',function() {
   	element  = $(this);
   	var id   = element.attr("data-id");
   	var info = 'comment_id=' + id;

   		element.removeClass('comments-likes');

   		$.ajax({
   			headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
		   type: "POST",
		   url: "{{ url('comments/likes') }}",
		   data: info,
		   success: function( data ) {


                $( '#collapse'+ id ).html(data);
                $('[data-toggle="tooltip"]').tooltip();

				if( data == '' ){
					$( '#collapse'+ id ).html("{{trans('misc.error')}}");
				}
				}//<-- $data
			});
   });

  @if( Auth::check() && Auth::user()->id == $response->user()->id )

  // Delete Photo
	 $("#deletePhoto").click(function(e) {
	   	e.preventDefault();

	   	var element = $(this);
		var url     = element.attr('data-url');

		element.blur();

		swal(
			{   title: "{{trans('misc.delete_confirm')}}",
			  type: "warning",
			  showLoaderOnConfirm: true,
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			   confirmButtonText: "{{trans('misc.yes_confirm')}}",
			   cancelButtonText: "{{trans('misc.cancel_confirm')}}",
			    closeOnConfirm: false,
			    },
			    function(isConfirm){
			    	 if (isConfirm) {
			    	 	window.location.href = url;
			    	 	}
			    	 });
			 });
	@endif

	//<<---- PAGINATION AJAX
        $(document).on('click','.pagination a', function(e){
			e.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			$.ajax({
				headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
					url: '{{ url("/") }}/ajax/comments?photo={{$response->id}}&page=' + page


			}).done(function(data){
				if( data ) {

					scrollElement('#gridComments');

					$('.gridComments').html(data);

					jQuery(".timeAgo").timeago();

					$('[data-toggle="tooltip"]').tooltip();
				} else {
					sweetAlert("{{trans('misc.error_oops')}}", "{{trans('misc.error')}}", "error");
				}
				//<**** - Tooltip
			});

		});//<<---- PAGINATION AJAX

    $(document).on('click', '#collection-md-close', 
        function () {
            $('#collections').modal('hide');
       }
    );
</script>

@endsection
