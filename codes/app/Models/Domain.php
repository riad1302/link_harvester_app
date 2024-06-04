<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';

    protected $fillable = ['name'];

    public function urls(): HasMany
    {
        return $this->hasMany(Url::class);
    }
}
