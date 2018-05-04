<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Jobs\FetchImage;
use App\Image;

class ImageController extends Controller
{
    //
    public function fetchCategory(){

    	$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_URL => "http://thecatapi.com/api/categories/list",
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_TIMEOUT => 30000,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		$xml = simplexml_load_string($response);

		$categories = array();
		$value = 0;
		foreach($xml->data->categories->category as $category) {
		    $obj = array(
		        "id" => $category->id,
		        "name" => $category->name
		    );
		    array_push($categories, $obj);
		    $image = new Image();
		    $this->dispatch(new FetchImage($image));
		}

		return redirect('/');
	}

	public function getData(){
		$cacheCount = Cache::get('count');
		$dbCount = Image::count();
		return view('results', compact('dbCount', 'cacheCount'));
	}
}
