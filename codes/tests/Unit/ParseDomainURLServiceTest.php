<?php

namespace Tests\Unit\Services;

use App\Services\ParseDomainURLService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParseDomainURLServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_urls_are_parsed_correctly()
    {
        $urls = "http://example.com\nhttp://example.org";
        $parseService = new ParseDomainURLService();
        $parseService->setURLs($urls);
        [$domainNames, $urlData] = $parseService->parse();

        $this->assertEquals(['example.com', 'example.org'], $domainNames);

        $this->assertCount(2, $urlData);
        $this->assertEquals(['url' => 'http://example.com', 'domain_name' => 'example.com'], $urlData[0]);
        $this->assertEquals(['url' => 'http://example.org', 'domain_name' => 'example.org'], $urlData[1]);
    }

    // Write more test methods to cover other scenarios
}
