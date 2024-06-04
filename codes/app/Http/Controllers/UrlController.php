<?php

namespace App\Http\Controllers;

use App\Actions\Url\ShowUrlDataAction;
use App\Actions\Url\StoreUrlDataAction;
use App\Http\Requests\UrlSubmissionRequest;
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
        $urls = $this->showUrlDataAction->execute($request);

        if ($request->ajax()) {
            return response()->json([
                'urls' => $urls,
                'pagination' => $urls->links()->render(),
            ]);
        }

        return view('urls.index', compact('urls'));
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
