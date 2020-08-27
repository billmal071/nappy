@foreach( $sponsoredImg as $image )

@php
    $index_uri = 2;
    $sponRootUrl = "http://istockphoto.7eer.net/c/1303643/258824/4205?u=";
    if ($image["referral_destinations"][1]["site_name"] == "istockphoto") {
        $index_uri = 1;
    }
    $imgHref =  $sponRootUrl.$image["referral_destinations"][$index_uri]["uri"];
    $imgSrc = $image["display_sizes"][1]["uri"];
@endphp
<!-- Start Item -->
<a 
    href="{{$imgHref}}"
    class="item hovercard image-btn istock-images" 
    target="blank_"
    style="cursor: pointer;"
    data-w="300"
    data-h="200"
>
    <img
        {{-- style="object-fit: cover;" --}}
        loading="lazy"
        src="{{$imgSrc}}"
        class="previewImage"/>
</a>
<!-- End Item -->
@endforeach

{{--{!! $images->render() !!}--}}