<?php

namespace App\Jobs;

use App\Services\ProcessUrlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUrlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $urlData;

    private array $domainNames;

    /**
     * Create a new job instance.
     */
    public function __construct(array $domainNames, array $urlData)
    {
        $this->urlData = $urlData;
        $this->domainNames = $domainNames;
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessUrlService $service): void
    {
        $service->setDomainNames($this->domainNames)
            ->setUrlData($this->urlData)
            ->execute();
    }
}
