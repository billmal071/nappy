<?php
    if ( Auth::check() ) {

        // FOLLOW ACTIVE
        $followActive = App\Models\Followers::where( 'follower', Auth::user()->id )
        ->where('following',$response->user()->id)
        ->where('status', '1')
        ->first();

        if( $followActive ) {
            $textFollow   = trans('users.following');
            $icoFollow    = '-ok';
            $activeFollow = 'btnFollowActive';
        } else {
            $textFollow   = trans('users.follow');
            $icoFollow    = '-plus';
            $activeFollow = '';
        }

         // LIKE ACTIVE
        $likeActive = App\Models\Like::where( 'user_id', Auth::user()->id )
            ->where('images_id',$response->id)
            ->where('status','1')
            ->first();

       if( $likeActive ) {
            $textLike   = trans('misc.unlike');
            $icoLike    = 'fa fa-heart';
            $statusLike = 'active';
       } else {
            $textLike   = trans('misc.like');
            $icoLike    = 'fa fa-heart-o';
            $statusLike = '';
       }

       // ADD TO COLLECTION
       $collections = App\Models\Collections::where('user_id',Auth::user()->id)->orderBy('id','asc')->get();

    }//<<<<---- *** END AUTH ***

    // All Images resolutions
    $stockImages = $response->stock;

    // Similar Photos
    $arrayTags  = explode(",",$response->tags);
    $countTags = count( $arrayTags );

    $images = App\Models\Images::where('categories_id',$response->categories_id)
        ->whereStatus('active')
        ->where(function($query) use ($arrayTags,$countTags){
            for( $k = 0; $k < $countTags; ++$k ){
                $query->orWhere('tags', 'LIKE', '%'.$arrayTags[$k].'%');
            }
        })
    ->where('id', '<>',$response->id)
    ->orderByRaw('RAND()')
    //->take(5)
    ->get();

    // $sponImages = App\Models\Images::where('sponsored', 'yes')->get();
    // Sponsored Photos
    $sponsoredImg = App\Helper\Sponsored::getPhotos($response->title);
?>

@if (Route::currentRouteName() != 'photo-details')
<meta property="og:type" content="website" />
<meta property="og:image:width" content="{{App\Helper::getWidth('public/uploads/preview/'.$response->preview)}}"/>
<meta property="og:image:height" content="{{App\Helper::getHeight('public/uploads/preview/'.$response->preview)}}"/>
<meta property="og:site_name" content="{{$settings->title}}"/>
<meta property="og:url" content="{{url("photo/$response->id").'/'.str_slug($response->title)}}"/>
<meta property="og:image" content="{{ asset('public/uploads/preview/') }}/{{$response->preview}}"/>
<meta property="og:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}"/>
<meta property="og:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}"/>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{ asset('public/uploads/preview/') }}/{{$response->preview}}" />
<meta name="twitter:title" content="{{ $response->title.' - '.trans_choice('misc.photos_plural', 1 ).' #'.$response->id }}" />
<meta name="twitter:description" content="{{ App\Helper::removeLineBreak( e( $response->description ) ) }}"/>
@endif

@if( Auth::check() )
<div class="modal fade" id="collections" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button id="collection-md-close" type="button" class="close">&times;</button>
                <h4 class="modal-title text-center" id="myModalLabel">
                    <strong>{{ trans('misc.add_collection') }}</strong>
                </h4>
             </div><!-- Modal header -->

             <div class="modal-body listWrap">

                <div class="collectionsData">
                    @if( $collections->count() != 0 )
                        @foreach ( $collections as $collection )

                            <?php

                                $collectionImages = $collection->collection_images->where('images_id',$response->id)->where('collections_id',$collection->id)->first();

                                if( !empty( $collectionImages ) ) {
                                    $checked = 'checked="checked"';
                                } else {
                                    $checked = null;
                                }
                            ?>
                            <div class="radio margin-bottom-15">
                                <label class="checkbox-inline padding-zero addImageCollection text-overflow" data-image-id="{{$response->id}}" data-collection-id="{{$collection->id}}">
                                <input class="no-show" name="checked" {{$checked}} type="checkbox" value="true">
                                <span class="input-sm">{{$collection->title}}</span>
                                </label>
                            </div>

                        @endforeach
                    @else
                        <div class="btn-block text-center no-collections">{{ trans('misc.no_have_collections') }}</div>
                    @endif
                </div><!-- collection data -->

                <small class="btn-block note-add @if( $collections->count() == 0 ) display-none @endif">* {{ trans('misc.note_add_collections') }}</small>

                <span class="label label-success display-none btn-block response-text"></span>

                <!-- form start -->
                <form method="POST" action="" enctype="multipart/form-data" id="addCollectionForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="image_id" value="{{ $response->id }}">

                 <!-- Start Form Group -->
                    <div class="form-group">
                      <label>{{ trans('admin.title') }}</label>
                        <input type="text" value="" name="title" id="titleCollection" class="form-control" placeholder="{{ trans('admin.title') }}">
                    </div><!-- /.form-group-->

                    <!-- Start form-group -->
                    <div class="form-group">

                        <div class="radio">
                            <label class="padding-zero">
                                <input type="radio" name="type" checked="checked" value="public">
                                {{ trans('misc.public') }}
                            </label>
                        </div>

                      <div class="radio">
                        <label class="padding-zero">
                            <input type="radio" name="type" value="private">
                            {{ trans('misc.private') }}
                        </label>
                      </div>

                    </div><!-- /.form-group -->

                    <!-- Alert -->
                    <div class="alert alert-danger alert-small display-none" id="dangerAlert">
                        <ul class="list-unstyled" id="showErrors"></ul>
                    </div><!-- Alert -->

                    <div class="btn-block text-center">
                        <button type="submit" class="btn btn-sm btn-success" id="addCollection">{{ trans('misc.create_collection') }} <i class="fa fa-plus"></i></button>
                    </div>

                </form>

              </div><!-- Modal body -->
            </div><!-- Modal content -->
        </div><!-- Modal dialog -->
    </div><!-- Modal -->
@endif

<div class="row">
    <div style="width: 95%; margin:auto;">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-7 col-xs-8">
                    <span>
                        <img src="{{ url('public/avatar/',$response->user()->avatar) }}" alt="User" class="img-circle profile-btn profile-img" data-user="{{$response->user()->username}}">
                        <em class="profile-btn" data-user="{{$response->user()->username}}" >{{'@'.$response->user()->username}}</em>
                    </span>
                </div>
                <div class="offset-lg-4 col-lg-3 offset-md-4 col-md-3 offset-sm-8 col-sm-4 offset-xs-8 col-xs-3 pad-l-zero">
                    @if( Auth::check() && $response->user()->id != Auth::user()->id && $response->user()->paypal_account != '' || Auth::guest()  && $response->user()->paypal_account != '' )
                        <button type="button" class="btn btn-outline-dark donate-btn" id="btnFormPP">{{trans('misc.donate')}}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="offset-lg-6 col-lg-6 offset-md-7 col-md-5 offset-sm-6 col-sm-5 offset-xs-6 col-xs-6">
        <div class="row">
            {{-- @if( Auth::check() && $response->status == 'active') --}}
            <div class="offset-lg-3 col-lg-3 col-sm-4 col-xs-3 pad-r-zero">
                @if( Auth::check()  )
                    <a href="#" class="btn btn-sm btnLike likeButton text-label action-btn {{$statusLike}}" data-id="{{$response->id}}" data-like="{{trans('misc.like')}}" data-unlike="{{trans('misc.unlike')}}">
                        <span class="btn-block text-center">
                            <i class="{{$icoLike}} color-red"></i>
                            <span class="textLike text-muted like-word"> {{$textLike}}</span>
                        </span>
                    </a>
                @else
                    <a href="{{url('login')}}" class="btn btn-sm btnLike text-label action-btn likeButton">
                        <span class="btn-block text-center textLike">
                            <i class="fa fa-heart-o text-center color-red"></i>
                            <span class="textLike text-muted like-word"> {{trans('misc.like')}}</span>
                        </span>
                    </a>
                @endif
            </div>

            <div class="offset-lg-6 col-lg-3 col-sm-4 offset-xs-3 col-xs-3 pad-r-zero">
                @if( Auth::check() )
                    <a id="coll-md-btn" data-toggle="modal" data-target="#collections" class="btn btn-sm blk-100 action-btn coll-btn">
                        <span class="btn-block text-center">
                            <i class="fa fa-plus text-center text-muted">
                                <span class="coll-word">{{trans('misc.collection')}}</span>
                            </i>
                        </span>
                    </a>
                @else
                    <a href="{{url('login')}}" class="btn btn-sm blk-100 action-btn coll-btn">
                        <span class="btn-block text-center">
                            <i class="fa fa-plus text-muted text-center">
                                <span class="coll-word">{{trans('misc.collection')}}</span>
                            </i>
                        </span>
                    </a>
                @endif
            </div>
            {{-- @endif --}}

            <!-- btn-group -->
            <div class="offset-lg-6 col-lg-4 col-sm-4 col-xs-3">
                <div class="btn-group btn-block margin-bottom-20 download-btn-div">

                    @if (
                        $response->item_for_sale == 'free' || 
                        Auth::check() && Auth::user()->id == $response->user_id && 
                        $response->item_for_sale == 'free'
                    )
                    <!-- btn-free -->
                    <button type="button" class="btn btn-success btn-sm btn-block dropdown-toggle text-label" id="downloadBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cloud-download myicon-right"></i> {{trans('misc.download')}}
                    </button>

                    <ul class="dropdown-menu arrowDownload dd-close btn-block">
                        @foreach( $stockImages as $stock )
                        <?php
                            switch( $stock->type ) {
                            case 'small':
                                $_size          = trans('misc.s');
                                break;
                            case 'medium':
                                $_size          = trans('misc.m');
                                break;
                            case 'large':
                                $_size          = trans('misc.l');
                                break;
                            }
                        ?>
                        <li>
                            {{--<a href="{{url('download',$stock->token)}}/{{$stock->type}}">
                                <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} 
                                <span class="pull-right">{{$stock->size}}</span>
                            </a> --}}
                            @if (Auth::check())
                                <button onclick="downloadAjax(this);" data-img-url="{{url('download',$stock->token)}}/{{$stock->type}}" data-img-id="{{$response->id}}">
                                    <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} &nbsp;
                                    <span class="pull-right">{{$stock->size}}</span>
                                </button>
                            @else
                                <button onclick="downloadToLogin();">
                                    <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} 
                                    <span class="pull-right">{{$stock->size}}</span>
                                </button>
                            @endif
                            
                        </li>
                        @endforeach
                      </ul>
                    <!-- btn-free -->
                    @else
                        <!-- btn-sale -->
                        <button type="button" class="btn btn-success btn-sm btn-block dropdown-toggle" id="downloadBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            @if (Auth::check() && Auth::user()->purchases()->where('images_id',$response->id)->count() != 0
                                || Auth::check() && Auth::user()->id == $response->user_id
                            )
                                <i class="fa fa-cloud-download myicon-right"></i> {{trans('misc.download')}}
                            @else
                                <i class="fa fa-shopping-cart myicon-right"></i> {{trans('misc.buy')}} {{\App\Helper::amountFormat($response->price)}}
                                <small class="sm-currency-code">{{$settings->currency_code}}</small>
                            @endif

                        </button>

                         <ul class="dropdown-menu arrowDownload dd-close btn-block">
                            @foreach( $stockImages as $stock )
                            <?php
                            switch( $stock->type ) {
                                case 'small':
                                    $_size          = trans('misc.s');
                                    break;
                                case 'medium':
                                    $_size          = trans('misc.m');
                                    break;
                                case 'large':
                                    $_size          = trans('misc.l');
                                    break;
                                }
                            ?>
                            <li>
                                {{-- <a href="{{url('purchase',$stock->token)}}/{{$stock->type}}">
                                    <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} 
                                    <span class="pull-right">{{$stock->size}}</span>
                                </a> --}}
                                @if (Auth::check())
                                    <button onclick="downloadAjax(this);" data-img-url="{{url('purchase',$stock->token)}}/{{$stock->type}}" data-img-id="{{$response->id}}">
                                        <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} &nbsp;
                                        <span class="pull-right">{{$stock->size}}</span>
                                    </button>
                                @else
                                    <a href="{{url('login')}}">
                                        <span class="label label-default myicon-right">{{$_size}}</span> {{$stock->resolution}} 
                                        <span class="pull-right">{{$stock->size}}</span>
                                    </a>
                                @endif
                            </li>
                            @endforeach
                          </ul>
                        <!-- btn-sale -->
                    @endif
                </div>
            <!-- End btn-group -->
             </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xs-12 stock-div">
        @if (isset($response))
            <img src="{{ url('public/uploads/medium',$stockImages{1}->name) }}" class="img-fluid mx-auto stock-img" alt="Responsive image">
        @endif
        
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xs-12 text-center">
        <span class="text-label">
            <i class="fa fa-eye" aria-hidden="true"></i>
            <span class="like_{{$response->id}}">{{App\Helper::formatNumber($response->visits()->count())}}</span>
            {{trans('misc.views')}}
        </span>
        <i class="fa fa-circle mid-dot"></i>
        <span class="text-label">
            <i class="fa fa-heart" aria-hidden="true"></i>
            <span class="like_{{$response->id}}">{{App\Helper::formatNumber($response->likes()->count())}}</span>
            {{trans('misc.likes')}}
        </span>
        <i class="fa fa-circle mid-dot"></i>
        <span class="text-label">
            <i class="icon icon-Download myicon-right"></i>
            <span class="download_{{$response->id}}">{{App\Helper::formatNumber($response->downloads()->count())}}</span>
            {{trans('misc.downloads')}}
        </span>
    </div>
</div>

<div class="row">
    <div class="container rel-img-div">
        <h6 class="text-center">{{trans('misc.related_photo')}}</h6>
        @if( $images->count() != 0 )
        <!-- Start Block -->
        <div class="btn-block margin-bottom-20 po">
            <div class="rel-flex-img flex-images btn-block margin-bottom-40">
                @include('includes.images-related')
            </div>
        </div>
        <!-- End Block -->
        @endif
    </div>
</div>

<div class="row">
    <div class="container spon-img-div">
        <h6 class="text-center">{{trans('misc.sponsored_photo')}}</h6>
        @if( count($sponsoredImg) != 0 )
        <div class="btn-block margin-bottom-20 po">
            <div class="spon-flex-img flex-images btn-block margin-bottom-40">
                @include('includes.images-sponsored')
            </div>
        </div>
        @endif
    </div>
    <div class="container spon-div">
        <p class="spon-text">Premium photos by iStock | Use code NAPPY15 for 15% off</p>
    </div>
</div>


@if( Auth::check() &&  isset($response->user()->id) && Auth::user()->id == $response->user()->id )
    <div class="row">
        <div class="row margin-bottom-20">
            <div class="col-md-3 col-md-offset-3">
                <a class="btn btn-success btn-block margin-bottom-5 photo-alter-btn"><i class="fa fa-pencil myicon-right "></i> {{trans('admin.edit')}}</a>
            </div>
            <div class="col-md-3">
                <a class="btn btn-danger btn-block" id="deletePhoto" data-url="{{ url('delete/photo',$response->id) }}"><i class="fa fa-times-circle myicon-right "></i> {{trans('admin.delete')}}</a>
            </div>
        </div>
    </div>
@endif


 @if( Auth::check() && $response->user()->id != Auth::user()->id && $response->user()->paypal_account != '' || Auth::guest()  && $response->user()->paypal_account != '' )
 <form id="form_pp" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="return" value="{{url('photo',$response->id)}}">
    <input type="hidden" name="cancel_return"   value="{{url('photo',$response->id)}}">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="item_name" value="{{trans('misc.support').' @'.$response->user()->username}} - {{$settings->title}}" >
    <input type="hidden" name="business" value="{{$response->user()->paypal_account}}">
    <input type="submit">
</form>
@endif

<script type="text/javascript">
    $(document).ready(function( $ ) {
        $('.profile-btn').click(function() {
            var usrNm = $(this).data('user');
            var loc = URL_BASE + "/" + usrNm;
            history.replaceState("", "", "/" + usrNm);
            window.location.href = loc;
        });

        $('.rel-flex-img').flexImages({ maxRows: 1, truncate: true });
        $('.spon-flex-img').flexImages({ maxRows: 1, truncate: true });

        @if( Auth::check() && Auth::user()->id == $response->user()->id )
            // Edit Photo
            $('.photo-alter-btn').click(function() {
                history.go(-goHistoryCount);
                window.location.replace('{{ url('edit/photo',$response->id) }}');
            });

            // Delete Photo
            $("#deletePhoto").click(function(e) {
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

    });

    function downloadAjax(e) {
        var imgUrl = $(e).data('img-url');
        var imgId = $(e).data('img-id');

        $.ajax({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
           type: "GET",
           url: imgUrl,
           dataType: 'json',
           data: null,
           success: function( response ) {
                if (typeof response.path === 'undefined') {
                    alert(response.msg);
                } else {
                    fetch(URL_BASE + '/' + response.path)
                        .then(resp => resp.blob())
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            // the filename you want
                            a.download = response.name;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);

                            $('.download_' + imgId).html(response.count);
                        })
                        .catch(() => alert('An error occured while downloading.'));
                }
           }
       });
    }

    function downloadToLogin() {
        // When this section is rendered by modal, need to clear history for opening the modal
        if ($('#modalImageDeatails-btn').length > 0) {
            history.back();
        }

        setTimeout(function(){
            window.location.href = "{{ url('login') }}";
        }, 100);
    }
</script>