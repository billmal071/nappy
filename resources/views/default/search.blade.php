@extends('app')

@section('title'){{ e($title) }}@endsection

@php
    $sponsoredImg = App\Helper\Sponsored::getPhotos($q);
@endphp

@section('content')
    <div
        style="height: 50vh;"
        class="jumbotron index-header jumbotron_set jumbotron-cover position-relative">
      <div class="container wrap-jumbotron">
        <h1 class="title-site title-sm" style="position: absolute; top: 30%; left: 50%; transform: translateX(-50%); z-index: 99; color: white !important;">
            {{ trans('misc.search') }}
        </h1>
        {{-- <p class="subtitle-site none-overflow"><strong>"{{$q}}"</strong></p> --}}

        <form
            role="search"
            autocomplete="off"
            action="{{ url('search') }}"
            method="get">
            <div
                style="position: absolute; top: 60%; left: 50%; transform: translateX(-50%);"
                class="input-group input-group-lg searchBar">
                <span class="input-group-btn">
                    <button class="btn btn-flat" type="submit" id="btnSearch">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </span>
                <input
                    type="text"
                    autocomplete="off"
                    name="q"
                    class="form-control"
                    value="{{$q}}"
                    id="btnItems"
                    placeholder="{{trans('misc.title_search_bar')}}">
            </div>
        </form>
      </div>
    </div>

    <div class="container-fluid margin-bottom-40">
        <div class="row">
            <div class="col-md-12">
                <h2
                    class="text-center position-relative none-overflow margin-bottom-30"
                    style="font-family: 'Lato'; font-weight: normal;">
                    {{-- {{ trans('misc.result_of') }} "{{ $q }}" <small>{{ $total }} {{ trans_choice('misc.images_plural',$total) }}</small> --}}
                    {{-- @if( $images->total() != 0 ) --}}
                    @if( $total != 0 )
                        "{{ $q }}"
                    @else
                        {{-- {{ trans('misc.no_result_for', ['q' => $q]) }} --}}
                        {{ trans('misc.no_result_for_custom') }}
                        <span style="font-weight: 900;">
                            "{{ $q }}"
                        </span>
                    @endif
                </h2>
                @include('includes.gallery')
            </div><!-- col-md-12 -->
        </div><!-- row -->
        @if( count($sponsoredImg) != 0 )
            <div class="row">
                <div class="container spon-img-div">
                    <h6 class="text-center">{{trans('misc.sponsored_photo')}}</h6>
                    <div class="btn-block margin-bottom-20 po">
                        {{-- <div class="rel-flex-img flex-images btn-block margin-bottom-40" id="sponsored-div"> --}}
                        <div class="spon-flex-img flex-images btn-block margin-bottom-40" id="sponsored-div">
                        </div>
                    </div>
                </div>
                <div class="container spon-div">
                    <p class="spon-text">Premium photos by iStock | Use code NAPPY15 for 15% off</p>
                </div>
            </div>
        @endif
    </div><!-- container -->
@endsection

@section('javascript')
    <script src="{{ asset('public/js/custom/gallery.js') }}"></script>

    <script type="text/javascript">
        jQuery(document).ready(function( $ ) {

            // Fetched Sponsored Photos
            var searchKey = $('#btnItems').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('sponsored')}}",
                data: { keyword: searchKey }
            }).done(function(data){
                $('#sponsored-div').append(data.sponsoredImg);
                $('.spon-flex-img').flexImages({ maxRows: 1, truncate: true });
            });
        });

        $('#imagesFlex').flexImages({ rowHeight: 320 });

        //<<---- PAGINATION AJAX
        $(document).on('click','.pagination a', function(e){
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $.ajax({
                headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                    url: '{{ url("/") }}/ajax/search?q={{$q}}&page=' + page


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
