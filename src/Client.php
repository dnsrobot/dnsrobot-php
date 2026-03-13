<?php

declare(strict_types=1);

namespace DNSRobot;

/**
 * PHP client for the DNS Robot API (https://dnsrobot.net).
 *
 * Provides access to 11 DNS and network tools including DNS lookups,
 * WHOIS queries, SSL certificate checks, and email authentication
 * validation (SPF, DKIM, DMARC).
 *
 * No API key required. Uses only built-in PHP extensions (curl, json).
 *
 * @see https://dnsrobot.net DNS Robot - 53 free online DNS and network tools
 */
class Client
{
    public const VERSION = '0.1.0';

    private string $baseUrl;
    private int $timeout;
    private string $userAgent;

    public function __construct(
        string $baseUrl = 'https://dnsrobot.net/api',
        int $timeout = 30,
        ?string $userAgent = null
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->userAgent = $userAgent ?? 'dnsrobot-php/' . self::VERSION;
    }

    /**
     * Perform a DNS lookup.
     *
     * @see https://dnsrobot.net/dns-lookup
     */
    public function dnsLookup(string $domain, string $recordType = 'A', string $dnsServer = '8.8.8.8'): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('dns-query', [
            'domain' => $domain,
            'recordType' => $recordType,
            'dnsServer' => $dnsServer,
        ]);
    }

    /**
     * Retrieve WHOIS registration data.
     *
     * @see https://dnsrobot.net/whois-lookup
     */
    public function whoisLookup(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('whois', ['domain' => $domain]);
    }

    /**
     * Check SSL/TLS certificate.
     *
     * @see https://dnsrobot.net/ssl-checker
     */
    public function sslCheck(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('ssl-certificate', ['domain' => $domain]);
    }

    /**
     * Validate SPF record.
     *
     * @see https://dnsrobot.net/spf-checker
     */
    public function spfCheck(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('spf-checker', ['domain' => $domain]);
    }

    /**
     * Check DKIM record.
     *
     * @see https://dnsrobot.net/dkim-checker
     */
    public function dkimCheck(string $domain, ?string $selector = null): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        $payload = ['domain' => $domain];
        if ($selector !== null) {
            $payload['selector'] = $selector;
        }

        return $this->post('dkim-checker', $payload);
    }

    /**
     * Validate DMARC record.
     *
     * @see https://dnsrobot.net/dmarc-checker
     */
    public function dmarcCheck(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('dmarc-checker', ['domain' => $domain]);
    }

    /**
     * Retrieve MX records.
     *
     * @see https://dnsrobot.net/mx-lookup
     */
    public function mxLookup(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('mx-lookup', ['domain' => $domain]);
    }

    /**
     * Retrieve nameserver records.
     *
     * @see https://dnsrobot.net/ns-lookup
     */
    public function nsLookup(string $domain): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('domain is required');
        }

        return $this->post('ns-lookup', ['domain' => $domain]);
    }

    /**
     * Look up IP geolocation data.
     *
     * @see https://dnsrobot.net/ip-lookup
     */
    public function ipLookup(string $ip): array
    {
        if (empty($ip)) {
            throw new \InvalidArgumentException('ip is required');
        }

        return $this->post('ip-info', ['ip' => $ip]);
    }

    /**
     * Fetch and analyze HTTP response headers.
     *
     * @see https://dnsrobot.net/http-headers
     */
    public function httpHeaders(string $url): array
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('url is required');
        }

        if (!preg_match('#^https?://#', $url)) {
            $url = 'https://' . $url;
        }

        return $this->post('http-headers', ['url' => $url]);
    }

    /**
     * Check if a TCP port is open.
     *
     * @see https://dnsrobot.net/port-checker
     */
    public function portCheck(string $host, int $port): array
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('host is required');
        }
        if ($port <= 0 || $port > 65535) {
            throw new \InvalidArgumentException('port must be 1-65535');
        }

        return $this->get('port-check', ['host' => $host, 'port' => (string) $port]);
    }

    private function post(string $endpoint, array $payload): array
    {
        $url = $this->baseUrl . '/' . $endpoint;
        $body = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: ' . $this->userAgent,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("DNS Robot API connection error: $error");
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("DNS Robot API error: HTTP $httpCode from $endpoint: $response");
        }

        return json_decode($response, true);
    }

    private function get(string $endpoint, array $params): array
    {
        $url = $this->baseUrl . '/' . $endpoint . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => [
                'User-Agent: ' . $this->userAgent,
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("DNS Robot API connection error: $error");
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("DNS Robot API error: HTTP $httpCode from $endpoint: $response");
        }

        return json_decode($response, true);
    }
}
