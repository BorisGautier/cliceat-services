<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'coverage' => 'integer',
        'status' => 'integer',
        'branch_promotion_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function branch_promotion(): HasMany
    {
        return $this->hasMany(BranchPromotion::class);
    }

    public function table(): HasMany
    {
        return $this->hasMany(Table::class, 'branch_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

}
