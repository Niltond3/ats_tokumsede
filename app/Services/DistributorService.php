<?php

namespace App\Services;

use App\Models\Distribuidor;
use Illuminate\Support\Facades\Cache;

class DistributorService
{
    /**
     * Get the effective distributor ID considering stock unions
     * @param int $distributorId Original distributor ID
     * @return int Main distributor ID or original ID if no union exists
     */
    public function getEffectiveDistributorId($distributorId)
    {
        return Cache::remember("effective_distributor_{$distributorId}", 3600, function() use ($distributorId) {
            $distributor = Distribuidor::find($distributorId);
            return $distributor->getMainDistributorIdAttribute() ?? $distributorId;
        });
    }

    /**
     * Get all distributors in a stock union
     * @param int $distributorId Main distributor ID
     * @return array Array of distributor IDs in union
     */
    public function getUnionDistributorIds($distributorId)
    {
        return Cache::remember("union_distributors_{$distributorId}", 3600, function() use ($distributorId) {
            $distributor = Distribuidor::find($distributorId);
            if ($distributor->stockUnionsAsMain()->exists()) {
                return $distributor->stockUnionsAsMain()
                    ->pluck('secondary_distributor_id')
                    ->push($distributor->id)
                    ->toArray();
            }
            return [$distributorId];
        });
    }

    /**
     * Validate if distributor exists and is active
     * @param int $distributorId Distributor ID to validate
     * @return bool Validation result
     */
    public function validateDistributor($distributorId)
    {
        return Cache::remember("distributor_valid_{$distributorId}", 3600, function() use ($distributorId) {
            return Distribuidor::where('id', $distributorId)
                ->where('status', Distribuidor::ATIVO)
                ->exists();
        });
    }
}
