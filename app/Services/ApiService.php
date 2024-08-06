<?php

// app/Services/ApiService.php

namespace App\Services;
use GuzzleHttp\Client;

use App\Jobs\PostDataBatchJob;

class ApiService
{
    protected $client;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://apiv2.jne.co.id:10205',
            'timeout'  => 5.0,
        ]);

        $this->username = env('JNE_API_USERNAME');
        $this->password = env('JNE_API_KEY');
    }

    public function postDataBatch(array $resis, int $batchSize = 50, int $sleepInterval = 500, int $sleepSeconds = 2)
    {
        $totalResiProcessed = 0;
        $batchCount = 0;

        // Membagi resi menjadi beberapa batch
        foreach (array_chunk($resis, $batchSize) as $batch) {
            $batchCount++;
            $totalResiProcessed += count($batch);

            // Kirim batch ke queue
            PostDataBatchJob::dispatch($batch);

            // Jika total resi yang sudah diproses lebih dari atau sama dengan sleepInterval, sleep selama sleepSeconds
            if ($totalResiProcessed >= $sleepInterval) {
                sleep($sleepSeconds);
                $totalResiProcessed = 0; // Reset counter setelah sleep
            }
        }

        return [
            'total_batches' => $batchCount,
            'total_resis' => $totalResiProcessed,
        ];
    }

}
