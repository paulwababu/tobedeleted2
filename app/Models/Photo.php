<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['id', 'deleted_at', 'updated_at'];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function ($col){
            $col->uuid = Str::orderedUuid()->toString();
        });
    }
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // photo unit
    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
