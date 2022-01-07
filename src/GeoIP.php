<?php
declare(strict_types=1);

namespace Nubium\IpTools;

use GeoIp2\Exception\AddressNotFoundException;

/**
 * Service pro praci s geo IP.
 */
class GeoIP
{
	private string $myIp;
	private GeoIPFacade $geoIPFacade;


	public function __construct(string $myIp, GeoIPFacade $geoIPFacade)
	{
		$this->myIp = $myIp;
		$this->geoIPFacade = $geoIPFacade;
	}


	/**
	 * Vrati kod zeme pro IP navstevnika z requestu.
	 */
	public function getCountryCode(): string
	{
		return $this->geoIPFacade->getCountryCodeForIp($this->myIp);
	}


	public function getASN(): ?int
	{
		try {
			return $this->geoIPFacade->getAsnReader()->asn($this->myIp)->autonomousSystemNumber;
		} catch (AddressNotFoundException $e) {
			return null;
		}
	}
}
