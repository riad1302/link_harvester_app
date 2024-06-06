<?php

namespace Tests\Unit\Services;

use App\Models\Domain;
use App\Services\ParseDomainURLService;
use App\Services\ProcessUrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_url_processing()
    {
        // Simulate valid input data
        $urls = "http://example.com\nhttp://example.org";
        $parseService = new ParseDomainURLService();
        $parseService->setURLs($urls);
        [$domainNames, $urlData] = $parseService->parse();

        // Ensure correct domain names are parsed
        $this->assertEquals(['example.com', 'example.org'], $domainNames);

        // Ensure correct URL data is parsed
        $this->assertCount(2, $urlData);
        $this->assertEquals(['url' => 'http://example.com', 'domain_name' => 'example.com'], $urlData[0]);
        $this->assertEquals(['url' => 'http://example.org', 'domain_name' => 'example.org'], $urlData[1]);

        // Test domain and URL processing
        $processService = new ProcessUrlService();
        $processService->setDomainNames($domainNames)->setUrlData($urlData)->execute();

        // Ensure domains are stored correctly in the database
        $this->assertDatabaseCount('domains', 2);
        $this->assertDatabaseHas('domains', ['name' => 'example.com']);
        $this->assertDatabaseHas('domains', ['name' => 'example.org']);

        // Ensure URLs are associated with the correct domains
        $this->assertDatabaseCount('urls', 2);
        $this->assertDatabaseHas('urls', ['url' => 'http://example.com']);
        $this->assertDatabaseHas('urls', ['url' => 'http://example.org']);

        // Ensure URLs are unique
        $this->assertCount(2, Domain::all());
        $this->assertCount(1, Domain::first()->urls);
    }
}
