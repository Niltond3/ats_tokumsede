<?php

namespace App\Traits;

use App\Services\DistributorService;

trait UsesDistributorService
{
    protected DistributorService $distributorService;

    /**
     * Initialize the distributor service
     */
    public function initializeDistributorService()
    {
        $this->distributorService = app(DistributorService::class);
    }

    /**
     * Get effective distributor ID
     * @param int $distributorId
     * @return int
     */
    protected function getEffectiveDistributorId($distributorId)
    {
        return $this->distributorService->getEffectiveDistributorId($distributorId);
    }
}
