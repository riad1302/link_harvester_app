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
            ->sort($request->sort, $request->get('direction', 'asc'))
            ->orderBy('id', 'DESC')
            ->paginate(20);
    }
}
