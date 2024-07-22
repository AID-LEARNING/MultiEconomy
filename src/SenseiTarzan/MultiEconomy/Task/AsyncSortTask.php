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

namespace SenseiTarzan\MultiEconomy\Task;

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;
use SenseiTarzan\MultiEconomy\Utils\Format;
use function array_slice;
use function array_walk;
use function arsort;

/**
 * @internal MultiEconomy
 */
final class AsyncSortTask extends AsyncTask
{

	private ThreadSafeArray $data;

	public function __construct(private readonly string $economy, private readonly int $limit, array $data, $resolve)
	{
		$this->data = Format::arrayToThreadSafeArray($data);
		$this->storeLocal("resolve", $resolve);
	}

	public function onRun() : void
	{
		$all = (array) $this->data;
		array_walk($all, function (&$value) {
			$value = $value[$this->economy];
		});
		arsort($all);
		$this->setResult(Format::arrayToThreadSafeArray(array_slice($all, 0, $this->limit)));
	}

	public function onCompletion() : void
	{
		$resolve = $this->fetchLocal("resolve");
		$resolve($this->getResult());
	}

}
