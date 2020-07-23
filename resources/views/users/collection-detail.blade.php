@extends('app')

@section('title'){{ $title }}@endsection

@section('css')
<link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
    <div class="container wrap-jumbotron position-relative">
        <h1 class="title-site title-sm">{{ trans('misc.collection') }}</h1>
        <p class="subtitle-site margin-zero">
            <strong>{{ $collectionData->title  }} ({{number_format($images->total())}})</strong>
        </p>
        <p class="subtitle-site margin-zero"><strong>{{ trans('misc.by')  }} <a href="{{url($user->username, 'collections')}}">{{$user->username}}</a></strong></p>
       
        @if( Auth::check() && Auth::user()->id == $collectionData->user_id )
            <p class="subtitle-site">
                <a href="#" title="{{trans('admin.edit')}}" class="btn btn-success btn-sm myicon-right" data-toggle="modal" data-target="#collections-edit"><i class="fa fa-pencil myicon-right "></i> {{trans('admin.edit')}}</a>
                <a href="#" title="{{trans('admin.delete')}}" data-url="{{url('collection/delete',$collectionData->id)}}" class="btn btn-danger btn-sm actionDelete"><i class="fa fa-times-circle myicon-right "></i> {{trans('admin.delete')}}</a>
            </p>

            <!-- Start Modal -->    
            <div class="modal fade" id="collections-edit" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content"> 
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title text-center" id="myModalLabel">
                                <strong>{{ trans('admin.edit') }}</strong>
                            </h4>
                        </div><!-- Modal header -->

                        <div class="modal-body listWrap">

                            <!-- form start -->
                            <form method="POST" action="" enctype="multipart/form-data" id="editCollectionForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="id" value="{{ $collectionData->id }}">

                                <!-- Start Form Group -->
                                <div class="form-group">
                                    <label>{{ trans('admin.title') }}</label>
                                    <input type="text" value="{{ $collectionData->title }}" name="title" id="titleCollection" class="form-control" placeholder="{{ trans('admin.title') }}">
                                </div><!-- /.form-group-->

                                <!-- Start form-group -->
                                <div class="form-group">
                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" name="type" @if($collectionData->type == 'public' ) checked="checked" @endif  value="public">{{ trans('misc.public') }}
                                        </label>
                                    </div>
                                  
                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" name="type" @if($collectionData->type == 'private' ) checked="checked" @endif  value="private">{{ trans('misc.private') }}
                                        </label>
                                    </div>
                                </div><!-- /.form-group -->

                                <!-- Alert -->
                                <div class="alert alert-danger alert-small display-none" id="dangerAlert">
                                    <ul class="list-unstyled" id="showErrors"></ul>
                                </div><!-- Alert -->

                                <div class="btn-block text-center">
                                    <button class="btn btn-sm btn-success" id="editCollection1">{{ trans('misc.save_changes') }}</button>
                                </div>

                            </form>

                        </div><!-- Modal body -->
                    </div><!-- Modal content -->
                </div><!-- Modal dialog -->
            </div><!-- Modal -->
        @endif

    </div>
</div>

<div class="container margin-bottom-40">

<!-- Col MD -->
<div class="col-md-12 margin-top-20 margin-bottom-20">
    @include('includes.gallery')
</div>
<!-- /COL MD -->

</div><!-- container wrap-ui -->

@endsection

@section('javascript')
<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/js/custom/gallery.js') }}"></script>
<script type="text/javascript">

$('input').iCheck({
    radioClass: 'iradio_flat-green',
    checkboxClass: 'icheckbox_square-green',
});

 $('#imagesFlex').flexImages({ rowHeight: 220 });

    @if( Auth::check() && Auth::user()->id == $collectionData->user_id )
 
        $(".actionDelete").click(function(e) {
            e.preventDefault();

            var element = $(this);
            var url     = element.attr('data-url');

            element.blur();

            swal({
                title: "{{trans('misc.delete_confirm')}}",
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

    //<--------- Edit Collection
$("#editCollection1").on('click',function(e){
    var element     = $(this);

    e.preventDefault();
    element.blur();

    element.attr({'disabled' : 'true'});

    $('.wrap-loader').hide();

         $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           type: "POST",
           url: URL_BASE+"/collection/edit",
           dataType: 'json',
           data: $("#editCollectionForm").serialize(),
           success: function( result ){

            if( result.not_authorized == true ){
                $('#dangerAlert').remove()
                $('.wrap-loader').hide();
                window.location.reload();

            }

            if( result.success == true ){
                $('.wrap-loader').hide();
                window.location.reload();

            } else {
                $('.wrap-loader').hide();

                var error = '';
                for( $key in result.errors ){
                    error += '<li><i class="glyphicon glyphicon-remove myicon-right"></i> ' + result.errors[$key] + '</li>';
                }

                $('#showErrors').html(error);
                $('#dangerAlert').fadeIn(500)

                element.removeAttr('disabled');
            }
         }//<-- RESULT
       });//<--- AJAX
});//<----- CLICK
</script>
@endsection