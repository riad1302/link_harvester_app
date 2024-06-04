<?php

namespace App\Http\Controllers;

use App\Actions\Url\ShowUrlDataAction;
use App\Actions\Url\StoreUrlDataAction;
use App\Http\Requests\UrlSubmissionRequest;
use App\Models\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UrlController extends Controller
{
    public function __construct(
        protected ShowUrlDataAction $showUrlDataAction,
        protected StoreUrlDataAction $storeUrlDataAction
    ) {

    }

    public function index(Request $request): View|JsonResponse
    {
        $query = Url::query()->with('domain'); // Assuming the relationship is set up

        if ($request->has('search')) {
            $query->where('url', 'like', '%' . $request->search . '%')
                ->orWhereHas('domain', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }

        if ($request->has('sort')) {
            $sortColumn = $request->sort;
            $sortDirection = $request->get('direction', 'asc');
            if ($sortColumn === 'domain') {
                $query->join('domains', 'urls.domain_id', '=', 'domains.id')
                    ->orderBy('domains.name', $sortDirection)
                    ->select('urls.*');
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }
        }

        $items = $query->paginate(10);

        //return view('urls.index', compact('items'));
        if ($request->ajax()) {
            return response()->json($items);
        }
        return view('urls.index', compact('items'));
//        $urls = $this->showUrlDataAction->execute($request);
//
//        if ($request->ajax()) {
//            return response()->json([
//                'urls' => $urls,
//                'pagination' => $urls->links()->render(),
//            ]);
//        }
//
//        return view('urls.index', compact('urls'));
    }

    public function create(): View
    {
        return view('urls.create');
    }

    public function store(UrlSubmissionRequest $request): JsonResponse
    {
        try {
            $result = $request->validated();
            $this->storeUrlDataAction->execute($result['urls']);

            return response()->json(['success' => true, 'message' => 'Creation successful']);
        } catch (Throwable $exception) {
            Log::error('Error_storing_URLs: ', ['exception' => $exception]);

            return response()->json(['success' => false, 'message' => 'An error occurred while processing your request.'], 500);
        }
    }
}
