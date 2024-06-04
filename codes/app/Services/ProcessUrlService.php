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
                $existingDomains = [];
                $batchSize = config('constans.batch_size');

                // Step 1: Find all existing domains in one query
                $existingDomains = Domain::whereIn('name', $this->domainNames)->pluck('id', 'name')->toArray();

                // Step 2: Identify new domains
                $newDomains = array_diff($this->domainNames, array_keys($existingDomains));
                if (! empty($newDomains)) {
                    $newDomainsData = [];
                    foreach ($newDomains as $name) {
                        $newDomainsData[] = ['name' => $name, 'created_at' => now(), 'updated_at' => now()];
                    }

                    // Insert new domains in batches
                    foreach (array_chunk($newDomainsData, $batchSize) as $batch) {
                        Domain::insert($batch);
                    }

                    // Fetch IDs of newly inserted domains
                    $newDomains = Domain::whereIn('name', $newDomains)->pluck('id', 'name')->toArray();
                    $existingDomains = array_merge($existingDomains, $newDomains);
                }
                // Prepare URL data with domain IDs
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

                // Step 3: Insert URLs while ensuring uniqueness in batches
                foreach (array_chunk($urlInsertData, $batchSize) as $batch) {
                    Url::upsert($batch, ['url'], ['updated_at']); // Use upsert for insert or update
                }
            });

        } catch (\Exception $exception) {
            Log::channel('Process_url_service')->error($exception->getMessage(), [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }
}
