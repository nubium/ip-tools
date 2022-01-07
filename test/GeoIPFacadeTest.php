<?php
declare(strict_types=1);

namespace Nubium\IpTools\Test;

use Nubium\IpTools\GeoIPFacade;
use PHPUnit\Framework\TestCase;

class GeoIPFacadeTest extends TestCase
{
	/**
	 * @dataProvider localOrInvalidIpProvider
	 */
	public function testLocalIpMatchesLocalRange(string $ip, string $countryCode): void
	{
		$geoIp = \Mockery::mock(GeoIPFacade::class)
			->makePartial()
			->shouldAllowMockingProtectedMethods();

		$geoIp->shouldReceive('fetchCountryCode')->never()->getMock();

		$this->assertSame($countryCode, $geoIp->getCountryCodeForIp($ip));
	}

	/**
	 * @return string[][]
	 */
	public function localOrInvalidIpProvider(): array
	{
		return [
			['127.0.0.1', GeoIPFacade::LOCAL_IP],
			['172.19.24.200', GeoIPFacade::LOCAL_IP],
			['172.19.24.200', GeoIPFacade::LOCAL_IP],
			['172.20.0.1', GeoIPFacade::LOCAL_IP],
			['192.168.5.103', GeoIPFacade::LOCAL_IP],
			['10.0.5.10', GeoIPFacade::LOCAL_IP],
			['10.5.5.12', GeoIPFacade::LOCAL_IP],
			['', GeoIPFacade::UNKNOWN_IP],
			['foo', GeoIPFacade::UNKNOWN_IP],
		];
	}


	/**
	 * @dataProvider countryIpProvider
	 */
	public function testCountryIpsMatchWithGeoipExtension(string $ip): void
	{
		$geoIp = \Mockery::mock(GeoIPFacade::class)
			->makePartial()
			->shouldAllowMockingProtectedMethods();

		$geoIp->shouldReceive('fetchCountryCode')->with($ip)->once()->andReturn('tr')->getMock();

		$this->assertSame('tr', $geoIp->getCountryCodeForIp($ip));
	}

	/**
	 * @return string[][]
	 */
	public function countryIpProvider(): array
	{
		return [
			['131.20.16.8'],
			['12.13.14.15'],
			['249.10.64.76'],
		];
	}
}
