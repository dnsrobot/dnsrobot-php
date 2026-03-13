# dnsrobot/dnsrobot

PHP client for the [DNS Robot](https://dnsrobot.net) API — 53 free online DNS and network tools.

No API key required. Uses only built-in PHP extensions (curl, json).

## Install

```bash
composer require dnsrobot/dnsrobot
```

## Quick Start

```php
use DNSRobot\Client;

$dr = new Client();

// DNS lookup
$result = $dr->dnsLookup('example.com');
print_r($result['resolvedIPs']);

// WHOIS lookup
$whois = $dr->whoisLookup('example.com');
echo $whois['registrar']['name'];

// SSL certificate check
$ssl = $dr->sslCheck('github.com');
echo "Days left: " . $ssl['leafCertificate']['daysToExpire'];

// Email authentication
$spf = $dr->spfCheck('gmail.com');
echo "SPF Grade: " . $spf['grade'];
```

## Available Methods

| Method | Description | Tool Page |
|--------|-------------|-----------|
| `dnsLookup($domain)` | DNS record lookup | [DNS Lookup](https://dnsrobot.net/dns-lookup) |
| `whoisLookup($domain)` | WHOIS registration data | [WHOIS Lookup](https://dnsrobot.net/whois-lookup) |
| `sslCheck($domain)` | SSL/TLS certificate check | [SSL Checker](https://dnsrobot.net/ssl-checker) |
| `spfCheck($domain)` | SPF record validation | [SPF Checker](https://dnsrobot.net/spf-checker) |
| `dkimCheck($domain)` | DKIM record check | [DKIM Checker](https://dnsrobot.net/dkim-checker) |
| `dmarcCheck($domain)` | DMARC record validation | [DMARC Checker](https://dnsrobot.net/dmarc-checker) |
| `mxLookup($domain)` | MX record lookup | [MX Lookup](https://dnsrobot.net/mx-lookup) |
| `nsLookup($domain)` | Nameserver lookup | [NS Lookup](https://dnsrobot.net/ns-lookup) |
| `ipLookup($ip)` | IP geolocation | [IP Lookup](https://dnsrobot.net/ip-lookup) |
| `httpHeaders($url)` | HTTP header analysis | [HTTP Headers](https://dnsrobot.net/http-headers) |
| `portCheck($host, $port)` | TCP port check | [Port Checker](https://dnsrobot.net/port-checker) |

## Links

- **Homepage**: [dnsrobot.net](https://dnsrobot.net)
- **All 53 Tools**: [dnsrobot.net/all-tools](https://dnsrobot.net/all-tools)
