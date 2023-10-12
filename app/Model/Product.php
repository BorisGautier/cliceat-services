<?php

namespace App\Model;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'set_menu' => 'integer',
        'popularity_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getPriceAttribute($price): float
    {
        return (float)Helpers::set_price($price);
    }

    public function getDiscountAttribute($discount): float
    {
        return (float)Helpers::set_price($discount);
    }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeVisible($query)
    {
        return $query->where('visibility', '=', 1);
    }

    public function scopeProductType($query, $type)
    {
        if ($type == 'veg') {
            return $query->where('product_type', 'veg');
        } elseif ($type == 'non_veg') {
            return $query->where('product_type', 'non_veg');
        }
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function rating(): HasMany
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class)->latest();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function product_by_branch(): HasMany
    {
        return $this->hasMany(ProductByBranch::class)->where(['branch_id' => auth('branch')->id()]);
    }

    public function branch_product(): HasOne
    {
        return $this->hasOne(ProductByBranch::class)->where(['branch_id' => Config::get('branch_id')]);
    }

    public function scopeBranchProductAvailability($query)
    {
        return $query->whereHas('branch_product', function ($q) {
            $q->where('is_available', 1);
        });
    }

    public function branch_products(): HasMany
    {
        return $this->hasMany(ProductByBranch::class)->where(['branch_id' => session()->get('branch_id') ?? 1]);
    }

    public function main_branch_product(): HasOne
    {
        return $this->hasOne(ProductByBranch::class)->where(['branch_id' => 1]);
    }
    public function sub_branch_product(): HasOne
    {
        return $this->hasOne(ProductByBranch::class)->where(['branch_id' => auth('branch')->id()]);
    }

}
