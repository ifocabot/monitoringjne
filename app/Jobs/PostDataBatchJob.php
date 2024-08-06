<?php

// app/Jobs/PostDataBatchJob.php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Utils;
use DateTime;

class PostDataBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batch;
    protected $username;
    protected $password;

    public function __construct(array $batch)
    {
        $this->batch = $batch;
        $this->username = env('JNE_API_USERNAME');
        $this->password = env('JNE_API_KEY');
    }

    public function handle()
    {
        $client = new Client([
            'base_uri' => 'https://apiv2.jne.co.id:10205',
            'timeout'  => 5.0,
        ]);

        $promises = [];

        foreach ($this->batch as $resi) {
            $promises[] = $client->postAsync("/tracing/api/list/v1/cnote/{$resi}", [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'username' => $this->username,
                    'api_key' => $this->password,
                ],
            ])->then(function ($response) use ($resi) {
                $responseBody = $response->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                if (empty($responseData)) {
                    Log::warning("Empty response for resi: {$resi}");
                    return;
                }


                $cnoteData = [
                    'cnote_number' => $resi,
                    'latitude' => $responseData['cnote']['lat'] ?? null,
                    'longitude' => $responseData['cnote']['long'] ?? null,
                    'photo' => $responseData['cnote']['photo'] ?? null,
                    'pod_code' => $responseData['cnote']['pod_code'] ?? null,
                    'city_name' => $responseData['cnote']['city_name'] ?? null,
                    'cust_type' => $responseData['cnote']['cust_type'] ?? null,
                    'signature' => $responseData['cnote']['signature'] ?? null,
                    'cnote_date' => $responseData['cnote']['cnote_date'] ?? null,
                    'keterangan' => $responseData['cnote']['keterangan'] ?? null,
                    'pod_status' => $responseData['cnote']['pod_status'] ?? null,
                    'price_per_kg' => $responseData['cnote']['priceperkg'] ?? null,
                    'last_status' => $responseData['cnote']['last_status'] ?? null,
                    'service_type' => $responseData['cnote']['servicetype'] ?? null,
                    'cnote_amount' => $responseData['cnote']['cnote_amount'] ?? null,
                    'cnote_origin' => $responseData['cnote']['cnote_origin'] ?? null,
                    'cnote_weight' => $responseData['cnote']['cnote_weight'] ?? null,
                    'shipping_cost' => $responseData['cnote']['shippingcost'] ?? null,
                    'cnote_cust_no' => $responseData['cnote']['cnote_cust_no'] ?? null,
                    'cnote_pod_date' => $responseData['cnote']['cnote_pod_date'] ?? null,
                    'freight_charge' => $responseData['cnote']['freight_charge'] ?? null,
                    'insurance_amount' => $responseData['cnote']['insuranceamount'] ?? null,
                    'reference_number' => $responseData['cnote']['reference_number'] ?? null,
                    'cnote_destination' => $responseData['cnote']['cnote_destination'] ?? null,
                    'cnote_goods_descr' => $responseData['cnote']['cnote_goods_descr'] ?? null,
                    'estimate_delivery' => $responseData['cnote']['estimate_delivery'] ?? null,
                    'cnote_pod_receiver' => $responseData['cnote']['cnote_pod_receiver'] ?? null,
                    'cnote_receiver_name' => $responseData['cnote']['cnote_receiver_name'] ?? null,
                    'cnote_services_code' => $responseData['cnote']['cnote_services_code'] ?? null,
                ];

                $cnoteDetailsData = isset($responseData['detail'][0]) ? [
                    'cnote_number' => $resi,
                    'cnote_date' => $responseData['detail'][0]['cnote_date'] ?? null,
                    'cnote_origin' => $responseData['detail'][0]['cnote_origin'] ?? null,
                    'cnote_weight' => $responseData['detail'][0]['cnote_weight'] ?? null,
                    'cnote_shipper_city' => $responseData['detail'][0]['cnote_shipper_city'] ?? null,
                    'cnote_shipper_name' => $responseData['detail'][0]['cnote_shipper_name'] ?? null,
                    'cnote_receiver_city' => $responseData['detail'][0]['cnote_receiver_city'] ?? null,
                    'cnote_receiver_name' => $responseData['detail'][0]['cnote_receiver_name'] ?? null,
                    'cnote_shipper_addr1' => $responseData['detail'][0]['cnote_shipper_addr1'] ?? null,
                    'cnote_shipper_addr2' => $responseData['detail'][0]['cnote_shipper_addr2'] ?? null,
                    'cnote_shipper_addr3' => $responseData['detail'][0]['cnote_shipper_addr3'] ?? null,
                    'cnote_receiver_addr1' => $responseData['detail'][0]['cnote_receiver_addr1'] ?? null,
                    'cnote_receiver_addr2' => $responseData['detail'][0]['cnote_receiver_addr2'] ?? null,
                    'cnote_receiver_addr3' => $responseData['detail'][0]['cnote_receiver_addr3'] ?? null,
                ] : [];

                $cnoteHistoriesData = isset($responseData['history']) ? array_map(function ($history) use ($resi) {
                    $date = DateTime::createFromFormat('d-m-Y H:i', $history['date']);
                    $formattedDate = $date ? $date->format('Y-m-d H:i:s') : null;

                    return [
                        'cnote_number' => $resi,
                        'code' => $history['code'],
                        'date' => $formattedDate,
                        'description' => $history['desc'] ?? null,
                        'photo1' => $history['photo1'] ?? null,
                        'photo2' => $history['photo2'] ?? null,
                        'photo3' => $history['photo3'] ?? null,
                        'photo4' => $history['photo4'] ?? null,
                        'photo5' => $history['photo5'] ?? null,
                    ];
                }, $responseData['history']) : [];

                $photoHistoriesData = isset($responseData['photo_history']) ? array_map(function ($photoHistory) use ($resi) {

                    $date = DateTime::createFromFormat('d-m-Y H:i', $photoHistory['date']);
                    $formattedDate = $date ? $date->format('Y-m-d H:i:s') : null;

                    return [
                        'cnote_number' => $resi,
                        'date' => $formattedDate,
                        'photo1' => $photoHistory['photo1'] ?? null,
                        'photo2' => $photoHistory['photo2'] ?? null,
                        'photo3' => $photoHistory['photo3'] ?? null,
                        'photo4' => $photoHistory['photo4'] ?? null,
                        'photo5' => $photoHistory['photo5'] ?? null,
                    ];
                }, $responseData['photo_history']) : [];

                // Dispatch the StoreDataJob
                StoreDataJob::dispatch($cnoteData, $cnoteDetailsData, $cnoteHistoriesData, $photoHistoriesData);

            })->otherwise(function (RequestException $e) use ($resi) {
                Log::error('API Request Error: ' . $e->getMessage());
                if ($e->hasResponse()) {
                    Log::error('.');
                }
            });
        }

        Utils::settle($promises)->wait();
    }
}
