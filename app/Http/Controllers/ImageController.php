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

		$client = new \GuzzleHttp\Client();
		$res = $client->get('http://thecatapi.com/api/categories/list');

		$response = (string)$res->getBody();

		$xml = simplexml_load_string($response);

		$categories = array();
		$value = 0;
		foreach($xml->data->categories->category as $category) {
		    $obj = array(
		        "id" => $category->id,
		        "name" => $category->name
		    );
		    array_push($categories, $obj);
		    $this->dispatch(new FetchImage((int) $obj["id"][0]));
		}

		return redirect('/');
	}

	public function getData(){
		$cacheCount = Cache::get('count');
		if(!($cacheCount))
			$cacheCount = 0;
		$dbCount = Image::count();
		return view('results', compact('dbCount', 'cacheCount'));
	}
}
