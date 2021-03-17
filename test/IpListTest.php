<?php
declare(strict_types=1);

namespace Nubium\IpTools\Test;

use Nubium\IpTools\IpList;
use PHPUnit\Framework\TestCase;

class IpListTest extends TestCase
{
	/**
	 * @param string[] $ipRanges
	 * @dataProvider providerTestIpRangesArray
	 */
	public function testIpRangesArray(array $ipRanges, ?string $insideIp, ?string $outsideIp, bool $shouldLog): void
	{
		if ($shouldLog) {
			$this->expectExceptionMessage('Unable to parse IP range');
		}

		$list = IpList::createFromArray($ipRanges);

		if ($insideIp !== null) {
			$this->assertTrue($list->contains($insideIp), "IP {$insideIp} should match");
		}
		if ($outsideIp !== null) {
			$this->assertFalse($list->contains($outsideIp), "IP {$outsideIp} should not match");
		}
	}


	/**
	 * @dataProvider providerTestIpRangesString
	 */
	public function testIpRangesString(string $ipRanges, ?string $insideIp, ?string $outsideIp, bool $shouldLog): void
	{
		if ($shouldLog) {
			$this->expectExceptionMessage('Unable to parse IP range');
		}

		$list = IpList::createFromString($ipRanges);

		if ($insideIp !== null) {
			$this->assertTrue($list->contains($insideIp), "IP {$insideIp} should match");
		}
		if ($outsideIp !== null) {
			$this->assertFalse($list->contains($outsideIp), "IP {$outsideIp} should not match");
		}
	}


	/**
	 * @return array<string, mixed[]>
	 */
	public function providerTestIpRangesString() : array
	{
		return [
			'from string single' => ['127.0.0.1/32', '127.0.0.1', '127.0.1.0', false],
			'from string multiple 1' => ['127.0.0.1/24;192.168.0.0/24', '127.0.0.1', '127.0.1.0', false],
			'from string multiple 2' => ['127.0.0.1/24;192.168.0.0/24', '192.168.0.0', '192.168.1.0', false],
			'from string config trash' => [' 127.0.0.1/24; ', '127.0.0.1', '127.0.1.0', false],
			'from string empty config' => ['', null, '127.0.1.0', false],

			'IP without mask' => ['127.0.0.1', '127.0.0.1', '127.0.0.0', false],
			'lower edge' => ['127.0.0.8/31', '127.0.0.8', '127.0.0.7', false],
			'upper edge' => ['127.0.0.8/31', '127.0.0.9', '127.0.0.10', false],
			'match everything' => ['0.0.0.0/0', '127.0.0.1', null, false],

			'ignore invalid IP' => ['127.0.0.1junk/0;192.168.0.0', '192.168.0.0', '127.0.0.1', true],
			'ignore invalid mask 1' => ['127.0.0.1/-10;192.168.0.0', '192.168.0.0', '127.0.0.1', true],
			'ignore invalid mask 2' => ['127.0.0.1/40;192.168.0.0', '192.168.0.0', '127.0.0.1', true],
		];
	}


	/**
	 * @return array<string, mixed[]>
	 */
	public function providerTestIpRangesArray() : array
	{
		return [
			'from array single' => [['127.0.0.1/32'], '127.0.0.1', '127.0.1.0', false],
			'from array multiple 1' => [['127.0.0.1/24', '192.168.0.0/24'], '127.0.0.1', '127.0.1.0', false],
			'from array multiple 2' => [['127.0.0.1/24', '192.168.0.0/24'], '192.168.0.0', '192.168.1.0', false],
			'from array config trash' => [[' 127.0.0.1/24  '], '127.0.0.1', '127.0.1.0', false],
			'from array empty config' => [[], null, '127.0.1.0', false],
		];
	}

}
