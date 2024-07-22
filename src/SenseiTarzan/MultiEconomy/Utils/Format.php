<?php

/*
 *
 *            _____ _____         _      ______          _____  _   _ _____ _   _  _____
 *      /\   |_   _|  __ \       | |    |  ____|   /\   |  __ \| \ | |_   _| \ | |/ ____|
 *     /  \    | | | |  | |______| |    | |__     /  \  | |__) |  \| | | | |  \| | |  __
 *    / /\ \   | | | |  | |______| |    |  __|   / /\ \ |  _  /| . ` | | | | . ` | | |_ |
 *   / ____ \ _| |_| |__| |      | |____| |____ / ____ \| | \ \| |\  |_| |_| |\  | |__| |
 *  /_/    \_\_____|_____/       |______|______/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author AID-LEARNING
 * @link https://github.com/AID-LEARNING
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\MultiEconomy\Utils;

use pmmp\thread\ThreadSafeArray;
use pocketmine\utils\TextFormat;
use function array_values;
use function is_array;
use function str_replace;
use function strtolower;

class Format
{

	public static function nameToId(string $name) : string
	{
		return str_replace(array_values(TextFormat::COLORS), "", strtolower(str_replace([" "], ["_"], $name)));
	}

	public static function threadSafeArrayToArray(ThreadSafeArray $array) : array
	{
		$result = [];
		foreach ($array as $key => $value) {
			$result[$key] = $value;
		}
		return $result;
	}

	public static function arrayToThreadSafeArray(array $array) : ThreadSafeArray
	{
		$result = new ThreadSafeArray();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result[$key] = self::arrayToThreadSafeArray($value);
				continue;
			}
			$result[$key] = $value;
		}
		return $result;
	}

}
