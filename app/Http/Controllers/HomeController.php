<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input as Input;
use App\Http\Requests;
use App\Models\User;
use App\Models\Images;
use Illuminate\Http\Request;
use App\Models\AdminSettings;
use App\Models\Categories;
use App\Models\Query;
use App\Models\Collections;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $images     = Query::featuredImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl(),
                'selected' => 'featured'
            ];
        }

        return view('index.home', [
            'images' => $images,
            'selected' => 'featured'
        ]);

    }// End Method

    public function getVerifyAccount( $confirmation_code ) {


        if( Auth::guest()
        || Auth::check()
        && Auth::user()->activation_code == $confirmation_code
        && Auth::user()->status == 'pending'
        ) {
        $user = User::where( 'activation_code', $confirmation_code )->where('status','pending')->first();

        if( $user ) {

            $update = User::where( 'activation_code', $confirmation_code )
            ->where('status','pending')
            ->update( array( 'status' => 'active', 'activation_code' => '' ) );


            Auth::loginUsingId($user->id);

             return redirect('/')
                    ->with([
                        'success_verify' => true,
                    ]);
            } else {
            return redirect('/')
                    ->with([
                        'error_verify' => true,
                    ]);
            }
        }
    else {
             return redirect('/');
        }
    }// End Method

    public function getSearch() {

    $q = request()->get('q');

        $images = Query::searchImages();

        //<--- * If $q is empty or is minus to 1 * ---->
        if( $q == '' || strlen( $q ) <= 2 ){
            return redirect('/');
        }

        return view('default.search')->with($images);
    }// End Method

    public function latest(Request $request) {

        $images = Query::latestImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl(),
                'selected' => 'latest'
            ];
        }

        return view('index.latest', [
            'images' => $images,
            'selected' => 'latest'
        ]);

    }// End Method

    public function featured(Request $request) {

        $images = Query::featuredImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl(),
                'selected' => 'featured'
            ];
        }

        return view('index.featured', [
            'images' => $images,
            'selected' => 'featured'
        ]);

    }// End Method


    public function popular(Request $request) {

        $images = Query::popularImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl(),
                'selected' => 'popular'
            ];
        }

        return view('index.popular', [
            'images' => $images,
            'selected' => 'popular'
        ]);

    }// End Method

    public function commented(Request $request) {

        $images = Query::commentedImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl()
            ];
        }

        return view('index.commented', ['images' => $images]);

    }// End Method

    public function viewed(Request $request) {

        $images = Query::viewedImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl()
            ];
        }

        return view('index.viewed', ['images' => $images]);

    }// End Method

    public function downloads(Request $request) {

        $images = Query::downloadsImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl()
            ];
        }

        return view('index.downloads', ['images' => $images]);

    }// End Method

    public function category(Request $request, $slug) {

        $images = Query::categoryImages($slug);

        if($request->ajax()) {

            $nextPgUrl = '';
            if (!empty($images['images']->nextPageUrl())) {
                $urlArr = explode('?', $images['images']->nextPageUrl());
                $nextPgUrl = $urlArr[0].'?'.$urlArr[1];
            }

            return [
                'images' => view('includes.images')->with(['images' => $images['images']])->render(),
                'next_page' => $nextPgUrl
            ];
        }

        return view('default.category')->with($images);

    }// End Method

    public function tags($slug) {

     if( strlen( $slug ) > 1 ) {
        $settings = AdminSettings::first();

        $images = Query::tagsImages($slug);

        return view('default.tags-show')->with($images);
        } else {
            abort('404');
        }

    }// End Method

    public function cameras($slug) {

    if( strlen( $slug ) > 3 ) {
        $settings = AdminSettings::first();

        $images = Query::camerasImages($slug);

        return view('default.cameras')->with($images);

        } else {
            abort('404');
        }
    }// End Method

    public function colors($slug) {

        if( strlen( $slug ) == 6 ) {

            $settings = AdminSettings::first();

            $images = Query::colorsImages($slug);

            return view('default.colors')->with($images);

        } else {
            abort('404');
        }
    }// End Method

    public function collections(Request $request) {

        $settings = AdminSettings::first();

        $title       = trans('misc.collections').' - ';

       $data = Collections::has('collection_images')
       ->where('type','public')
        ->orderBy('id','desc')
        ->paginate( $settings->result_request );

        if( $request->input('page') > $data->lastPage() ) {
            abort('404');
        }

        return view('default.collections', [ 'title' => $title, 'data' => $data] );
    }//<--- End Method

    public function premium(Request $request) {

      $settings = AdminSettings::first();

      if ($settings->sell_option == 'off') {
          abort(404);
      }

        $images = Query::premiumImages();

        if($request->ajax()) {
            return [
                'images' => view('includes.images')->with(compact('images'))->render(),
                'next_page' => $images->nextPageUrl()
            ];
        }

        return view('index.premium', ['images' => $images]);

    }// End Method

    public function sponsored(Request $request) {

        $sponsoredImg = \App\Helper\Sponsored::getPhotos($request->keyword);

        if ($request->ajax()) {
            return [
                'sponsoredImg' => view('includes.images-sponsored')->with(compact('sponsoredImg'))->render(),
            ];
        }
    }

}
