<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorStockUnion extends Model
{
    protected $fillable = [
        'main_distributor_id',
        'secondary_distributor_id',
        'active'
    ];

    public function mainDistributor()
    {
        return $this->belongsTo(Distribuidor::class, 'main_distributor_id');
    }

    public function secondaryDistributor()
    {
        return $this->belongsTo(Distribuidor::class, 'secondary_distributor_id');
    }
}
