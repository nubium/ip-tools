<?php
declare(strict_types=1);

namespace Nubium\IpTools\Test;

use Nubium\IpTools\GeoIP;
use PHPUnit\Framework\TestCase;

class GeoIPTest extends TestCase
{
	/**
	 * @dataProvider localIpProvider
	 */
	public function testLocalIpMatchesLocalRange(string $ip): void
	{
		$geoIp = \Mockery::mock(GeoIP::class, [$ip]);
		$geoIp
			->makePartial()
			->shouldAllowMockingProtectedMethods()
			->shouldReceive('fetchCountryName')->never()->getMock();

		$this->assertSame('local', $geoIp->getCountryCode());
	}

	/**
	 * @return string[][]
	 */
	public function localIpProvider(): array
	{
		return [
			['127.0.0.1'],
			['172.19.24.200'],
			['172.19.24.200'],
			['172.20.0.1'],
			['192.168.5.103'],
			['10.0.5.10'],
			['10.5.5.12'],
		];
	}


	/**
	 * @dataProvider countryIpProvider
	 */
	public function testCountryIpsMatchWithGeoipExtension(string $ip): void
	{
		$geoIp = \Mockery::mock(GeoIP::class, [$ip]);
		$geoIp
			->makePartial()
			->shouldAllowMockingProtectedMethods()
			->shouldReceive('fetchCountryName')->with($ip)->once()->andReturn('turkey')->getMock();

		$this->assertSame('turkey', $geoIp->getCountryCode());
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
