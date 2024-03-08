<?php

namespace App\Jobs;

use App\Models\Price;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePriceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;

    /**
     * Create a new job instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->token);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.osmosis.zone/tokens/v2/price/' . $this->token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        Log::info(serialize($response));

        curl_close($curl);
        $data = json_decode($response, true);

        Price::create([
           'token' => $this->token,
           'price' => $data['price'],
           'change' => $data['change'],
        ]);

    }
}
