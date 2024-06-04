<?php

namespace App\Services;

class ParseDomainURLService
{
    private string $urls;

    public function setURLs(string $urls): self
    {
        $this->urls = $urls;

        return $this;
    }

    public function parse(): array
    {
        $domainNames = [];
        $urlData = [];
        $urls = explode(PHP_EOL, $this->urls);
        $urls = array_filter(array_map('trim', $urls));

        foreach ($urls as $url) {
            $parsedUrl = parse_url($url);
            $domainName = $parsedUrl['host'] ?? null;
            if ($domainName) {
                $domainNames[$domainName] = true;
                $urlData[] = ['url' => $url, 'domain_name' => $domainName];
            }
        }

        return [array_keys($domainNames), $urlData];
    }
}
