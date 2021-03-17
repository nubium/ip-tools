<?php
declare(strict_types=1);

namespace Nubium\IpTools;

/**
 * List IP rozsahu umoznujici kontrolovat, zda obsahuje IP adresu.
 */
class IpList
{
	/** @var int[][] */
	private array $ranges = [];

	/**
	 * Lokalni cache kontroly IP rozsahu.
	 *
	 * @var bool[]
	 */
	private array $isInRange = [];


	/**
	 * @param string[] $ranges
	 * @throws \RuntimeException
	 */
	public static function createFromArray(array $ranges): IpList
	{
		return self::createIpList($ranges);
	}


	/**
	 * @throws \RuntimeException
	 */
	public static function createFromString(string $rawRanges): IpList
	{
		$ranges = explode(';', $rawRanges);
		return self::createIpList($ranges);
	}


	public function contains(string $ip): bool
	{
		$ip = ip2long((string)$ip);
		if (!$ip) {
			return false;
		}
		if (!isset($this->isInRange[$ip])) {
			$result = false;
			foreach ($this->ranges as $range) {
				if ($this->matchIp($ip, $range)) {
					$result = true;
				}
			}
			$this->isInRange[$ip] = $result;
		}

		return $this->isInRange[$ip];
	}


	/**
	 * @param string[] $ranges
	 * @throws \RuntimeException
	 */
	private static function createIpList(array $ranges): IpList
	{
		$list = new self();
		$failed = [];
		foreach ($ranges as $range) {
			if (trim($range) == '') {
				continue;
			}
			$range = explode('/', $range);
			if (count($range) == 1) {
				$range[] = 32; // single IP mask
			}
			if (count($range) == 2) {
				$ip = ip2long(trim((string) $range[0]));
				$mask = (int)trim((string) $range[1]);
				if ($ip !== false && $mask >= 0 && $mask <= 32) {
					$list->ranges[] = [$ip, $mask];
				} else {
					$failed[] = $range;
				}
			}
		}
		if (count($failed) > 0) {
			throw new \RuntimeException('Unable to parse IP range');
		}
		return $list;
	}


	/**
	 * Kontroluje zda je IP adresa v rozsahu.
	 * @param int[] $range
	 */
	private function matchIp(int $ip, array $range): bool
	{
		if (!$ip || !$range) {
			return false;
		}
		$maskIP = $range[0];
		$maskBits = 32 - $range[1];

		return ($ip >> $maskBits) == ($maskIP >> $maskBits);
	}
}
