<?php
declare(strict_types=1);

namespace Nubium\IpTools;

/**
 * Service pro praci s geo IP.
 */
class GeoIP
{
	private string $myIp;


	public function __construct(?string $myIp)
	{
		$this->myIp = (string)$myIp;
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

		$countryName = $this->fetchCountryName($ip);
		return $countryName ? strtolower($countryName) : 'unknown';
	}


	public function getASN(): ?string
	{
		return (@geoip_asnum_by_name($this->myIp)) ?: null;
	}


	protected function fetchCountryName(string $ip): ?string
	{
		return (@geoip_country_code_by_name($ip)) ?: null;
	}
}
