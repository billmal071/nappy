@extends('app')

@section('css')

@stop

@php
@endphp

@section('content')
    <div class="jumbotron index-header jumbotron_set jumbotron-cover @if( Auth::check() ) session-active-cover @endif">
        <div class="container wrap-jumbotron position-relative">
            {{-- <h1 class="title-site vivify driveInTop delay-500" id="titleSite">{{$settings->welcome_text}}</h1> --}}
            <h1 class="subtitle-site vivify delay-600">
                <span class="home-header">
                    {{$settings->welcome_subtitle}}
                </span>
            </h1>

            <form role="search" autocomplete="off" action="{{ url('search') }}" method="get">
                <div class="input-group input-group-sm searchBar">
                    <span class="input-group-btn">
                        <button class="btn btn-flat" type="submit" id="btnSearch">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        name="q"
                        id="btnItems"
                        placeholder="{{trans('misc.title_search_bar')}}">
                </div>
            </form>

            <p class="subtitle-site vivify home-pop-search">
                {{trans('misc.popular_searches')}}
                <span><a href="{{ url('search').'?q='.trans('smile') }}">smile,</a></span>
                <span><a href="{{ url('search').'?q='.trans('family') }}">family,</a></span>
                <span><a href="{{ url('search').'?q='.trans('couple') }}">couple,</a></span>
                <span><a href="{{ url('search').'?q='.trans('business') }}">business.</a></span>
            </p>
        </div><!-- container wrap-jumbotron -->
            <span class="home-photoby">
                Photo by <a class="" id="photoBy" href="#">@phabstudio</a>
            </span>
    </div><!-- jumbotron -->

    <div class="container-fluid margin-bottom-40">
        <div id="gallery-div" class="row margin-bottom-20 gallery-container">
            <div style="margin-bottom: 1rem;" class="col-md-12 btn-block text-center">
                <button type="button" class="btn btn-link gallery-btn @if ($selected == 'latest') underscored @endif" data-type="latest">Latest</button>
                <button type="button" class="btn btn-link gallery-btn @if ($selected == 'popular') underscored @endif" data-type="popular">Popular</button>
                <button type="button" class="btn btn-link gallery-btn @if ($selected == 'featured') underscored @endif" data-type="featured">Featured</button>
            </div>
            @include('includes.gallery')
        </div><!-- row -->
    </div><!-- container wrap-ui -->
@endsection

@section('javascript')
    <script src="{{ asset('public/plugins/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('public/plugins/jquery.counterup/waypoints.min.js') }}"></script>
    <script src="{{ asset('public/js/loadingoverlay.js') }}"></script>
    <script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('public/js/custom/gallery.js') }}"></script>
    <script type="text/javascript">

        // $('#imagesFlex').flexImages({ rowHeight: 320, truncate: false });

        jQuery(document).ready(function( $ ) {
            $('.counter').counterUp({
                delay: 10, // the delay time in ms
                time: 1000 // the speed time in ms
            });
        });

        @if (session('success_verify'))
            swal({
                title: "{{ trans('misc.welcome') }}",
                text: "{{ trans('users.account_validated') }}",
                type: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
                });
        @endif

        @if (session('error_verify'))
            swal({
                title: "{{ trans('misc.error_oops') }}",
                text: "{{ trans('users.code_not_valid') }}",
                type: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
                });
        @endif

    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.gallery-btn').click(function () {
                var galleryUrl = '';
                switch ($(this).data('type')) {
                    case 'latest':
                        galleryUrl = "{{ url('latest')}}";
                    break;

                    case 'popular':
                        galleryUrl = "{{ url('popular')}}";
                    break;

                    case 'featured':
                        galleryUrl = "{{ url('featured')}}";
                    break;
                }

                $.get(galleryUrl, function(data){
                    $('#imagesGrid').children().remove();
                    $('.blank-gallery-div').remove();
                    $('.no-result').remove();

                    if (data.images != '') {
                        $('#imagesGrid').append(data.images);
                        $('.endless-pagination').data('next-page', data.next_page);
                        // $('#imagesGrid').flexImages({ rowHeight: 320, truncate: false });

                        if (data.selected == 'latest') {
                            $('*[data-type="latest"]').addClass('underscored');
                        } else {
                            $('*[data-type="latest"]').removeClass('underscored');
                        }

                        if (data.selected == 'popular') {
                            $('*[data-type="popular"]').addClass('underscored');
                        } else {
                            $('*[data-type="popular"]').removeClass('underscored');
                        }

                        if (data.selected == 'featured') {
                            $('*[data-type="featured"]').addClass('underscored');
                        } else {
                            $('*[data-type="featured"]').removeClass('underscored');
                        }

                    } else {
                        var blnkGall = '<div class="btn-block text-center blank-gallery-div">';
                        blnkGall += '<i class="icon icon-Picture ico-no-result"></i>';
                        blnkGall += '</div>';
                        blnkGall += '<h3 class="margin-top-none text-center no-result no-result-mg">';
                        blnkGall += '{{ trans("misc.no_images_published") }}';
                        blnkGall += '</h3>';

                        $('.endless-pagination').data('next-page', '');
                        $('#gallery-div').append(blnkGall);
                    }
                });
            });
        });
    </script>
@endsection
