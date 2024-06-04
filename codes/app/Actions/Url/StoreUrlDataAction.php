<?php

namespace App\Actions\Url;

use App\Jobs\ProcessUrlsJob;
use App\Services\ParseDomainURLService;
use App\Services\ProcessUrlService;
use Illuminate\Support\Facades\Log;
use Throwable;

class StoreUrlDataAction
{
    public function __construct(private ParseDomainURLService $parseDomainURLService)
    {

    }

    public function execute(string $urls): void
    {
        try {
            [$domainNames, $urlData] = $this->parseDomainURLService
                ->setURLs($urls)
                ->parse();

            //(new ProcessUrlService())->setDomainNames($domainNames)->setUrlData($urlData)->execute();
            dispatch(new ProcessUrlsJob($domainNames, $urlData));
        } catch (Throwable $exception) {
            Log::channel('Store_url_data_action')->error($exception->getMessage(), [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString(),
            ]);
        }

    }
}
