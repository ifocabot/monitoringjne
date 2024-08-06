<?php
// app/Http/Controllers/testingController.php

namespace App\Http\Controllers;

use App\Models\cnotes;
use App\Models\Resi;
use App\Services\ApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class testingController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function processResis()
    {
        // Mengambil nilai no_awb sebagai array
        $resis = Resi::pluck('no_awb')->take(5000)->toArray();

        // Log the resis for debugging
        Log::info('Processing Resis', ['resis' => $resis]);

        $totalResi = count($resis);
        $totalDuration = $this->apiService->postDataBatch($resis);

        return response()->json([
            'total_resi' => $totalResi,
            'total_duration' => $totalDuration,
        ]);
    }

    public function showResi(Request $request)
    {
        // Ambil parameter filter dari query string
        $filter = $request->query('filter'); // Contoh: 'filter' bisa bernilai '>1jam', '>2jam', dll.

        // Ambil data cnote dengan histori terakhir
        $cnoteData = cnotes::whereNull('pod_code')
            ->with(['histories' => function ($query) {
                $query->orderBy('date', 'desc');
            }])
            ->get();

        // Buat array untuk menyimpan hasil dengan selisih waktu dan status SLA
        $results = $cnoteData->map(function ($cnote) {
            $latestHistory = $cnote->histories->first();
            if ($latestHistory) {
                $lastUpdate = Carbon::parse($latestHistory->date);
                $now = Carbon::now();
                $diffInHours = $lastUpdate->diffInHours($now);
                $diffForHumans = $lastUpdate->diffForHumans($now, true);

                // Hitung SLA
                $cnoteSLA = Carbon::parse($cnote->cnote_date)->addDays(2); // Sesuaikan jika '2 Days' dinamis
                $isOverSLA = $now->greaterThan($cnoteSLA);
                $overSLADuration = $isOverSLA ? $cnoteSLA->diffForHumans($now, true) : null;

                return [
                    'cnote_number' => $cnote->cnote_number,
                    'last_update' => $latestHistory->date,
                    'diff_for_humans' => $diffForHumans,
                    'diff_in_hours' => $diffInHours,
                    'cnote_sla' => $cnote->estimate_delivery,
                    'cnote_date' => $cnote->cnote_date,
                    'is_over_sla' => $isOverSLA,
                    'over_sla_duration' => $overSLADuration
                ];
            }

            return [
                'cnote_number' => $cnote->cnote_number,
                'last_update' => null,
                'diff_for_humans' => null,
                'diff_in_hours' => null,
                'cnote_sla' => $cnote->estimate_delivery,
                'cnote_date' => $cnote->cnote_date,
                'is_over_sla' => false,
                'over_sla_duration' => null
            ];
        });

        // Terapkan filter jika ada
        if ($filter) {
            $results = $results->filter(function ($item) use ($filter) {
                $diffInHours = $item['diff_in_hours'];

                switch ($filter) {
                    case '>1jam':
                        return $diffInHours > 1;
                    case '>2jam':
                        return $diffInHours > 2;
                    case '>4jam':
                        return $diffInHours > 4;
                    case '>8jam':
                        return $diffInHours > 8;
                    case '>16jam':
                        return $diffInHours > 16;
                    case '>1hari':
                        return $diffInHours > 24;
                    case '>2hari':
                        return $diffInHours > 48;
                    case '>4hari':
                        return $diffInHours > 96;
                    default:
                        return true; // Jika filter tidak dikenali, tampilkan semua
                }
            });
        }

        return response()->json($results);
    }

    public function shoResiWithoutWeekend(Request $request){

    }
}
