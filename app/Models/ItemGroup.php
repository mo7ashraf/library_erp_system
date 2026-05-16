<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subgroups(): HasMany
    {
        return $this->hasMany(ItemSubgroup::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}