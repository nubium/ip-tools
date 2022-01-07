<?php
declare(strict_types=1);

namespace Nubium\IpTools;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

/**
 * Wrapper pro praci s geo IP.
 */
class GeoIPFacade
{

	const LOCAL_IP = 'local';
	const UNKNOWN_IP = 'unknown';

	private ?Reader $countryReader = null;
	private ?Reader $asnReader = null;
	private GeoIPReaderFactory $geoIPReaderFactory;

	public function __construct(GeoIPReaderFactory $geoIPReaderFactory)
	{
		$this->geoIPReaderFactory = $geoIPReaderFactory;
	}


	/**
	 * Vrati kod zeme pro zadane IP.
	 */
	public function getCountryCodeForIp(string $ip): string
	{
		if (@inet_pton($ip) === false) {
			return self::UNKNOWN_IP;
		}

		$ipList = IpList::createFromString('192.168.0.0/16;10.0.0.0/8;172.16.0.0/12;127.0.0.0/8');
		if ($ipList->contains($ip)) {
			return self::LOCAL_IP;
		}

		$countryName = $this->fetchCountryCode($ip);
		return $countryName ? strtolower($countryName) : self::UNKNOWN_IP;
	}

	public function getCountryReader(): Reader
	{
		return $this->countryReader ?? $this->countryReader = $this->geoIPReaderFactory->createCountryReader();
	}

	public function getAsnReader(): Reader
	{
		return $this->asnReader ?? $this->asnReader = $this->geoIPReaderFactory->createAsnReader();
	}

	protected function fetchCountryCode(string $ip): ?string
	{
		try {
			return $this->getCountryReader()->country($ip)->country->isoCode;
		} catch (AddressNotFoundException $e) {
			return null;
		}
	}
}
