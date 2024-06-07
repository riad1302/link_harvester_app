<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessUrlService
{
    private array $urlData;

    private array $domainNames;

    public function setDomainNames(array $domainNames): self
    {
        $this->domainNames = $domainNames;

        return $this;
    }

    public function setUrlData(array $urls): self
    {
        $this->urlData = $urls;

        return $this;
    }

    public function execute(): void
    {
        try {
            DB::transaction(function () {
                $batchSize = config('constants.batch_size');

                $existingDomains = $this->fetchExistingDomains($this->domainNames) ?? [];

                $existingDomains = $this->insertNewDomainsAndMerge($existingDomains, $batchSize);

                $this->insertUrls($existingDomains, $batchSize);
            });

        } catch (\Exception $exception) {
            $this->logError($exception);
        }
    }

    private function fetchExistingDomains(array $domainNames): array
    {
        return Domain::whereIn('name', $domainNames)->pluck('id', 'name')->toArray();
    }

    private function insertNewDomainsAndMerge(?array $existingDomains, int $batchSize): array
    {
        $newDomains = collect($this->domainNames)
            ->diff(collect($existingDomains)->keys())->all();

        if (! empty($newDomains)) {
            $newDomainsData = collect($newDomains)->map(fn ($name) => [
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            foreach (array_chunk($newDomainsData, $batchSize) as $batch) {
                Domain::insert($batch);
            }

            $newDomains = $this->fetchExistingDomains($newDomains);
            $existingDomains = array_merge($existingDomains, $newDomains);
        }

        return $existingDomains;
    }

    private function insertUrls(array $existingDomains, int $batchSize): void
    {
        $urlInsertData = [];
        foreach ($this->urlData as $data) {
            if (isset($existingDomains[$data['domain_name']])) {
                $urlInsertData[] = [
                    'url' => $data['url'],
                    'domain_id' => $existingDomains[$data['domain_name']],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($urlInsertData, $batchSize) as $batch) {
            Url::upsert($batch, ['url'], ['updated_at']);
        }
    }

    private function logError(\Exception $exception): void
    {
        Log::channel('Process_url_service')->error($exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
