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

    public function scopeSort($query, $sort, $sortDirection = 'asc')
    {
        return $query->when($sort, function ($query, $sort) use ($sortDirection) {
            if ($sort == 'domain') {
                $query->join('domains', 'urls.domain_id', '=', 'domains.id')
                    ->orderBy('domains.name', $sortDirection)
                    ->select('urls.*');
            } else {
                $query->orderBy($sort, $sortDirection);
            }
        });
    }
}
