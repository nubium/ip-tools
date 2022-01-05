<?php
declare(strict_types=1);

namespace Nubium\IpTools;

use GeoIp2\Database\Reader;

/**
 * Service pro praci s geo IP.
 */
class GeoIP
{
	private string $myIp;
	private Reader $reader;


	public function __construct(?string $myIp, string $maxmindDbPath = '/usr/share/GeoIP/GeoIP2-Country.mmdb')
	{
		$this->myIp = (string) $myIp;
		$this->reader = new Reader($maxmindDbPath);
	}


	/**
	 * Vrati kod zeme pro IP navstevnika z requestu.
	 */
	public function getCountryCode(): string
	{
		return $this->getCountryCodeForIp($this->myIp);
	}


	/**
	 * Vrati kod zeme pro zadane IP.
	 */
	public function getCountryCodeForIp(string $ip): string
	{
		$ipList = IpList::createFromString('192.168.0.0/16;10.0.0.0/8;172.16.0.0/12;127.0.0.0/8');
		if ($ipList->contains($ip)) {
			return 'local';
		}

		$countryName = $this->fetchCountryCode($ip);
		return $countryName ? strtolower($countryName) : 'unknown';
	}


	public function getASN(): ?int
	{
		return $this->reader->asn($this->myIp)->autonomousSystemNumber;
	}

	protected function fetchCountryCode(string $ip): ?string
	{
		return $this->reader->country($ip)->country->isoCode;
	}
}
