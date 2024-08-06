<?php

// app/Jobs/StoreDataJob.php

namespace App\Jobs;

use App\Models\cnote_details;
use App\Models\cnote_histories;
use App\Models\Cnotes;
use App\Models\photo_histories;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StoreDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cnoteData;
    protected $cnoteDetailsData;
    protected $cnoteHistoriesData;
    protected $photoHistoriesData;

    public function __construct(array $cnoteData, array $cnoteDetailsData, array $cnoteHistoriesData, array $photoHistoriesData)
    {
        $this->cnoteData = $cnoteData;
        $this->cnoteDetailsData = $cnoteDetailsData;
        $this->cnoteHistoriesData = $cnoteHistoriesData;
        $this->photoHistoriesData = $photoHistoriesData;
    }

    public function handle()
    {
        try {
            DB::transaction(function () {
                $cnote = Cnotes::updateOrCreate(['cnote_number' => $this->cnoteData['cnote_number']], $this->cnoteData);
                Log::info('Cnote Data saved: ' . json_encode($cnote->toArray()));

                if (!empty($this->cnoteDetailsData)) {
                    $cnoteDetail = cnote_details::updateOrCreate(['cnote_number' => $this->cnoteDetailsData['cnote_number']], $this->cnoteDetailsData);
                    Log::info('Cnote Details Data saved: ' . json_encode($cnoteDetail->toArray()));
                }

                if (!empty($this->cnoteHistoriesData)) {
                    foreach ($this->cnoteHistoriesData as $historyData) {
                        $cnoteHistory = cnote_histories::updateOrCreate(['cnote_number' => $historyData['cnote_number'], 'code' => $historyData['code']], $historyData);
                        Log::info('Cnote History Data saved: ' . json_encode($cnoteHistory->toArray()));
                    }
                }

                if (!empty($this->photoHistoriesData)) {
                    foreach ($this->photoHistoriesData as $photoHistoryData) {
                        $photoHistory = photo_histories::updateOrCreate(['cnote_number' => $photoHistoryData['cnote_number'], 'date' => $photoHistoryData['date']], $photoHistoryData);
                        Log::info('Photo History Data saved: ' . json_encode($photoHistory->toArray()));
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Error in transaction: ' . $e->getMessage());
        }
    }
}
