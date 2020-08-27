<?php

namespace App\Helper;

class Sponsored
{
    public static function getPhotos($phrase)
    {
       # Replace these values with your key and secret
        $api_key = env('GETTYIMAGES_API_KEY');
        $api_secret = env('GETTYIMAGES_API_SECRET');
        // $phrase = "dog";
        // $phrase = $_GET["s"];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.gettyimages.com/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=" . $api_key . "&client_secret=". $api_secret,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            exit;
        }
        $token_response = json_decode($response, true);
        $access_token = $token_response["access_token"];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            //CURLOPT_URL => "https://api.gettyimages.com/v3/search/images/creative?compositions=candid&ethnicity=black&exclude_editorial_use_only=false&fields=referral_destinations%2Csummary_set&number_of_people=one%2Ctwo%2Cgroup&orientations=Horizontal&&phrase=" . urlencode($phrase)."&sort_order=best_match&page_size=5",
            CURLOPT_URL => "https://api.gettyimages.com/v3/search/images/creative?compositions=candid&ethnicity=black&exclude_editorial_use_only=false&fields=comp%2Cdisplay_set%2Cpreview%2Creferral_destinations%2Csummary_set&number_of_people=one%2Ctwo%2Cgroup&orientations=Horizontal&&phrase=" . urlencode($phrase)."&sort_order=best_match&page_size=5",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",  
            CURLOPT_HTTPHEADER => array(
                "Api-Key: " . $api_key,
                "Authorization: Bearer " . $access_token,
                "Accept: application/json"
            )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            exit;
        }
        $search_response = json_decode($response, true);
        $images = $search_response["images"];
        
        return $images;

        // echo '<div class="" style="background:#fff;padding:0px;margin-bottom:10px;">';
        // for ($i = 0; $i < count($images); $i++) {
        //     $index_uri = 2;
        //     if($images[$i]["referral_destinations"][1]["site_name"] == "istockphoto"){
        //         $index_uri = 1;
        //     }
        //     if($i > 1){
        //         echo '<a href="http://istockphoto.7eer.net/c/1303643/258824/4205?u='.$images[$i]["referral_destinations"][$index_uri]["uri"].'" target="blank_" class="istock-images">';
        //         echo "<img src=". $images[$i]["display_sizes"][1]["uri"] . " >";
        //         echo "</a>";
        //     }
        //     else{
        //         echo '<a href="http://istockphoto.7eer.net/c/1303643/258824/4205?u='.$images[$i]["referral_destinations"][$index_uri]["uri"].'" target="blank_" class="istock-show">';
        //         echo "<img src=". $images[$i]["display_sizes"][1]["uri"] . " >";
        //         echo "</a>";
        //     }            
        // }
        // echo '<label style="margin:0;padding:0;">Premium photos by iStock | Use code NAPPY15 for 15% off</label>';
        // echo '</div>';
    }
}
