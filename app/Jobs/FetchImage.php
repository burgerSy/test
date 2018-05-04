<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Image;

use Illuminate\Support\Facades\Cache;

class FetchImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        //
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $curl = curl_init();

        $number = rand (10,20);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://thecatapi.com/api/images/get?format=xml&results_per_page=" . $number,
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

        if($xml)
            Cache::increment('count', $number, 720);

        foreach($xml->data->images->image as $image) {
            $url = $image->url;
            $input['url'] = $url;
            Image::create($input);
        }
    }
}
