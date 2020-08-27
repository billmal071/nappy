<?php
/*----------------------------------------------
 *  SHOW NUMBER NOTIFICATIONS IN BROWSER ( 1 )
 * --------------------------------------------
 */
 if( Auth::check() ) {

    // Notifications
    $notifications_count = App\Models\Notifications::where('destination',Auth::user()->id)->where('status','0')->count();

    if( $notifications_count != 0 ) {
        $totalNotifications = '('.( $notifications_count ).') ';
        $totalNotify = ( $notifications_count );
    } else {
        $totalNotifications = null;
        $totalNotify = null;
    }
 } else {
    $totalNotifications = null;
    $totalNotify = null;
 }

?>
<!DOCTYPE html>
<html lang="{{strtolower(config('app.locale'))}}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description_custom'){{ $settings->description }}">
    <meta name="keywords" content="@yield('keywords_custom'){{ $settings->keywords }}" />
    <link rel="shortcut icon" href="{{ asset('public/img/favicon.png') }}" />

    <title>
        {{$totalNotifications}}
        @section('title')
        @show
        @if( isset( $settings->title ) )
            {{$settings->title}}
        @endif
    </title>

    @include('includes.css_general')

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,100;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @yield('css')

    @include('includes.javascript_general')
    <link href="{{ asset('public/plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />

    @if( Auth::check() )
        <script type="text/javascript">
            //<----- Notifications
            function Notifications() {

                var _title = '@section("title")@show {{e($settings->title)}}';

                console.time('cache');

                $.get(URL_BASE+"/ajax/notifications", function( data ) {
                    if ( data ) {
                        //* Notifications */
                        if( data.notifications != 0 ) {
                            var totalNoty = data.notifications;
                            $('#noti_connect').html(data.notifications).fadeIn();
                        } else {
                            $('#noti_connect').html('').hide();
                        }

                        //* Error */
                        if( data.error == 1 ) { window.location.reload() }

                        var totalGlobal = parseInt( totalNoty );

                        if( data.notifications == 0 ) {
                            $('.notify').hide();
                            $('title').html( _title );
                        }

                        if( data.notifications != 0 ) {
                            $('title').html( "("+ totalGlobal + ") " + _title );
                        }

                    }//<-- DATA
                },'json');

                console.timeEnd('cache');
            }//End Function TimeLine

            timer = setInterval("Notifications()", 10000);
        </script>
    @endif

</head>

<body>
    @if( isset( $settings->google_analytics ) )
        <?php echo html_entity_decode($settings->google_analytics) ?>
    @endif

    <div class="popout font-default"></div>

    <div class="wrap-loader">
        <i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader"></i>
        <i class="fa fa-cog fa-spin fa-3x fa-fw cog-loader-small"></i>
    </div>

    @if(!Request::is('/') && !Request::is('search') )
        <form
            role="search"
            class="box_Search collapse"
            autocomplete="off"
            action="{{ url('search') }}"
            method="get"
            id="formShow">
            <div>
                <input
                    type="text"
                    name="q"
                    class="input_search form-control"
                    id="btnItems"
                    placeholder="{{trans('misc.search')}}">
                <button type="submit" id="_buttonSearch">
                    <i class="icon-search"></i>
                </button>
            </div><!--/.form-group -->
        </form><!--./navbar-form -->
    @endif

    @include('includes.navbar')

    @if( Auth::check() && Auth::user()->status == 'pending' )
        <div class="alert alert-danger text-center margin-zero border-group">
            <i class="icon-warning myicon-right"></i>
            {{trans('misc.confirm_email')}}
            <strong>{{ Auth::user()->email}}</strong>
        </div>
    @endif

    @yield('content')
    @include('includes.footer')
    {{-- @include('includes.javascript_general') --}}
    @yield('javascript')

    @include('includes.javascript_image_details')
    <script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}"></script>
    <script type="text/javascript">
        Cookies.set('cookieBanner');
        $(document).ready(function() {
            if (Cookies('cookiePolicySite'));
            else {
                $('.showBanner').fadeIn();
                $("#close-banner").click(function() {
                    $(".showBanner").slideUp(50);
                    Cookies('cookiePolicySite', true);
                });
            }

            // [WIP - masonry layout]
            function resizeGridItem(item, num){
                // let before = item.querySelector("#test-grid").complete;
                let grid = document.getElementsByClassName("grid-gallery")[0];
                let rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));
                let rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'));
                let rowSpan = Math.ceil((item.querySelector(".item").getBoundingClientRect().height+rowGap)/(rowHeight+rowGap));
                item.style.gridRowEnd = "span "+rowSpan;
                let xheight = item.getBoundingClientRect().height;
                // let after = item.querySelector("#test-grid").complete;
                // console.log(`${num}, ${before},  ${after}`);
                item.querySelector("#test-grid").style.height = xheight + "px";
            }

            function resizeAllGridItems(){
                let allItems = document.getElementsByClassName("content");
                for(x=0;x<allItems.length;x++){
                    resizeGridItem(allItems[x], x);
                }
            }

            function resizeInstance(instance){
                let item = instance.elements[0];
                resizeGridItem(item);
            }

            // window.onload = resizeAllGridItems();
            // window.addEventListener("resize", resizeAllGridItems);

            let allItems = document.getElementsByClassName("content");
            for(x=0;x<allItems.length;x++){
                imagesLoaded( allItems[x], resizeInstance);
            }
        });

        var goHistoryCount = 0;

        $(document).ready(function(){
            $(".previewImage").removeClass('d-none');
        });

        // $(document).on('click', '.image-btn', function() {
        $(document).on('click', '.image-btn', function() {
            var imgId = $(this).data('img-id');
            var imgDetailUrl = "{{ url('photo') }}/" + $(this).data('img-id');

            $.get('/photo/'+imgId, function(data){
                $("body").css({overflow: 'hidden'});
                let result = jQuery(data.detail).find("#ajaxmodal");
                $('#modal-body-id').children().remove();
                $('#modal-body-id').append(result.prevObject[0]);
                $('#modalImageDeatails-btn').click();
            });

            history.pushState("", "", "/photo/" + $(this).data('img-id'));

            // check if no more previous photos
            var firstImg = $("#imagesFlex").children().first().data("img-id")
            if (imgId == firstImg) {
                $(this).removeClass('active-prev-next-btn')
                $('.img-det-prev').addClass('inactive-prev-next-btn')
            } else {
                $(this).removeClass('inactive-prev-next-btn')
                $('.img-det-prev').addClass('active-prev-next-btn')
            }

            // check if no more next photos
            var lastImg = $("#imagesFlex").children().last().data("img-id")

            if (imgId == lastImg) {
                $(this).removeClass('active-prev-next-btn')
                $('.img-det-next').addClass('inactive-prev-next-btn')
            } else {
                $(this).removeClass('inactive-prev-next-btn')
                $('.img-det-next').addClass('active-prev-next-btn')
            }
        });

        // Event when selecting Previous photo
        $(document).on('click', '.img-det-prev', function() {
            var currLoc = window.location.href;
            var rtUrl = "{{ url('photo') }}/";
            var imgId = currLoc.replace(rtUrl, "");

            var prevId = $("#imagesFlex").find(`#${imgId}`).prev().data("img-id");
            var firstImg = $("#imagesFlex").children().first().data("img-id");

            // check if no more previous photos
            if (prevId == firstImg) {
                $(this).removeClass('active-prev-next-btn');
                $(this).addClass('inactive-prev-next-btn');
            } else {
                $(this).removeClass('inactive-prev-next-btn');
                $(this).addClass('active-prev-next-btn');
            }

            $('.img-det-next').removeClass('inactive-prev-next-btn');
            $('.img-det-next').addClass('active-prev-next-btn');

            var imgDetailUrl = rtUrl + prevId;
            $.get(imgDetailUrl, function(data){
                $('#modal-body-id').children().remove();
                let result = jQuery(data.detail).find("#ajaxmodal");
                $('#modal-body-id').append(result.prevObject[0]);
                $('.rel-flex-img').flexImages({ maxRows: 1, truncate: true });
                goHistoryCount++;
                history.pushState("", "", `/photo/${prevId}`);
            });
        });

        // Event when selecting Next photo
        $(document).on('click', '.img-det-next', function() {
            var currLoc = window.location.href;
            var rtUrl = "{{ url('photo') }}/";
            var imgId = currLoc.replace(rtUrl, "");

            var nextId = $("#imagesFlex").find(`#${imgId}`).next().data("img-id");
            var lastImg = $("#imagesFlex").children().last().data("img-id");

            // check if no more next photos
            if (nextId == lastImg) {
                $(this).removeClass('active-prev-next-btn');
                $('.img-det-next').addClass('inactive-prev-next-btn');
            } else {
                $(this).removeClass('inactive-prev-next-btn');
                $('.img-det-next').addClass('active-prev-next-btn');
            }

            $('.img-det-prev').removeClass('inactive-prev-next-btn');
            $('.img-det-prev').addClass('active-prev-next-btn');

            var imgDetailUrl = rtUrl + nextId;
                $.get(imgDetailUrl, function(data){
                $('#modal-body-id').children().remove();
                let result = jQuery(data.detail).find("#ajaxmodal");
                $('#modal-body-id').append(result.prevObject[0]);
                $('.rel-flex-img').flexImages({ maxRows: 1, truncate: true });
                goHistoryCount++;
                history.pushState("", "", `/photo/${nextId}`);
            });
        });

        $(document).on('click', '#coll-md-btn', function() {
            $('input').iCheck({
                radioClass: 'iradio_flat-green',
                checkboxClass: 'icheckbox_square-green',
            });
        });

        $(document).on('ifChecked', 'input', function() {
            var _element = $(this).closest('label');
            var imageID  = _element.attr("data-image-id");
            var collectionID  = _element.attr("data-collection-id");

            $.ajax({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: URL_BASE+'/collection/'+collectionID+'/i/'+imageID,
                dataType: 'json',
                data: null,
                success: function( response ) {
                    $('#collections').modal('hide');
                    $('.popout')
                        .addClass('alert-success')
                        .html(response.data);
                }
            });
        });

    </script>
</body>
</html>
