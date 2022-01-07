<?php
declare(strict_types=1);

namespace Nubium\IpTools;

use GeoIp2\Database\Reader;

class GeoIPReaderFactory
{
	private string $countryDbPath;
	private string $asnDbPath;

	public function __construct(
		string $countryDbPath = '/usr/share/GeoIP/GeoIP2-Country.mmdb',
		string $asnDbPath = '/usr/share/GeoIP/GeoLite2-ASN.mmdb'
	) {
		$this->countryDbPath = $countryDbPath;
		$this->asnDbPath = $asnDbPath;
	}

	public function createCountryReader(): Reader
	{
		return new Reader($this->countryDbPath);
	}

	public function createAsnReader(): Reader
	{
		return new Reader($this->asnDbPath);
	}
}
