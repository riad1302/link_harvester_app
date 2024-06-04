<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Url extends Model
{
    use HasFactory;

    protected $table = 'urls';

    protected $fillable = ['url', 'domain_id'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where('url', 'like', '%'.$search.'%')
                ->orWhereHas('domain', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
        });
    }
}
