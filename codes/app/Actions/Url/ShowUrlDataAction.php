<?php

namespace App\Actions\Url;

use App\Models\Url;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowUrlDataAction
{
    public function execute($request): LengthAwarePaginator
    {
        return Url::query()->with('domain:id,name')
            ->search($request->search)
            ->orderBy('url')
            ->paginate(20);
    }
}
