@php
    // $id = 177; 
    // $output = $images
    //             ->map(function($image){ return $image; })
    //             ->filter(function($image){ return $image->id == $id; })
    //             ->first();
    // $response = App\Models\Images::findOrFail($id);
@endphp
<!-- Modal: modalQuickView -->
<button
    id="modalImageDeatails-btn"
    type="button"
    class="btn btn-primary"
    data-toggle="modal"
    data-target="#modalImageDetails">
    Launchmodal
</button>
<div
    class="modal fade"
    id="modalImageDetails"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div id="modal-body-id" class="modal-body">
            </div>
            <a class="previous-button img-det-prev active-prev-next-btn" role="button">
                <i class="fa fa-angle-left fa-3x fa-fw"></i>
            </a>
            <a class="next-button img-det-next active-prev-next-btn" role="button">
                <i class="fa fa-angle-right fa-3x fa-fw"></i>
            </a>
            <a class="img-detail-close" data-dismiss="modal" role="button">
                <i class="fa fa-times fa-2x fa-fw"></i>
            </a>
        </div>
    </div>
</div>

@if( $images->total() != 0 )
    <div
        id="imagesGrid"
        class="endless-pagination flex-images grid-gallery btn-block margin-bottom-40"
        data-next-page="{{ $images->nextPageUrl() }}">
        @include('includes.images')
    </div><!-- Image Flex -->
    <div id="below-img"></div>
@else
    @if (Route::currentRouteName() != 'search')
        <div class="btn-block text-center">
            <i class="icon icon-Picture ico-no-result"></i>
        </div>
    @endif

    <h3 class="margin-top-none text-center no-result no-result-mg">
        @if (Route::currentRouteName() == 'home' || Route::currentRouteName() == 'latest')
            {{ trans('misc.no_images_published') }}
        @elseif (Route::currentRouteName() == 'profile')
            {{ trans('users.user_no_images') }}
        @elseif (
                Route::currentRouteName() == 'category' || Route::currentRouteName() == 'featured' || 
                Route::currentRouteName() == 'popular' || Route::currentRouteName() == 'commented' ||
                Route::currentRouteName() == 'viewed' || Route::currentRouteName() == 'downloads' ||
                Route::currentRouteName() == 'premium'
            )
            {{ trans('misc.no_results_found') }}
        @elseif (Route::currentRouteName() == 'collection-details')
            {{ trans('misc.collection_empty') }}
        @endif
    </h3>
@endif