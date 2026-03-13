<?php

declare(strict_types=1);

namespace DNSRobot\Tests;

use DNSRobot\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testCustomConstructor(): void
    {
        $client = new Client('https://example.com/api', 10, 'test/1.0');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testVersion(): void
    {
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', Client::VERSION);
    }

    public function testAllMethodsExist(): void
    {
        $methods = [
            'dnsLookup', 'whoisLookup', 'sslCheck', 'spfCheck',
            'dkimCheck', 'dmarcCheck', 'mxLookup', 'nsLookup',
            'ipLookup', 'httpHeaders', 'portCheck',
        ];
        foreach ($methods as $method) {
            $this->assertTrue(method_exists($this->client, $method), "Missing method: $method");
        }
    }

    public function testDnsLookupRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->dnsLookup('');
    }

    public function testWhoisRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->whoisLookup('');
    }

    public function testSslRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->sslCheck('');
    }

    public function testSpfRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->spfCheck('');
    }

    public function testDkimRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->dkimCheck('');
    }

    public function testDmarcRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->dmarcCheck('');
    }

    public function testMxRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->mxLookup('');
    }

    public function testNsRequiresDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->nsLookup('');
    }

    public function testIpRequiresIp(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->ipLookup('');
    }

    public function testHttpHeadersRequiresUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->httpHeaders('');
    }

    public function testPortCheckRequiresHost(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->portCheck('', 80);
    }

    public function testPortCheckRequiresValidPort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->portCheck('example.com', 0);
    }
}
