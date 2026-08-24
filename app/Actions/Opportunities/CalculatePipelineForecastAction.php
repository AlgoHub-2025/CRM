<?php

namespace App\Actions\Opportunities;

use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;

class CalculatePipelineForecastAction
{
    /**
     * Calculate the total forecast value for a specific stage.
     *
     * @param string $stageId
     * @return float
     */
    public function execute(string $stageId): float
    {
        // Use DB-level aggregation as agreed, preventing memory issues with large datasets.
        return (float) Opportunity::where('stage_id', $stageId)
            ->sum(DB::raw('value * (probability / 100.0)'));
    }
}
