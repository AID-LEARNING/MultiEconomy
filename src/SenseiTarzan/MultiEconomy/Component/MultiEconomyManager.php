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

namespace SenseiTarzan\MultiEconomy\Component;

use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Economy\Economy;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\Path\PathScanner;
use Symfony\Component\Filesystem\Path;
use function is_null;

final class MultiEconomyManager
{

	use SingletonTrait;

	/** @var Economy[] $listEconomy */
	private array $listEconomy = [];

	public function __construct(Main $plugin)
	{
		self::setInstance($this);
		foreach (PathScanner::scanDirectoryToConfig(Path::join($plugin->getDataFolder(), "Economy"), ["yml"]) as $info) {
			if (is_null($info->get("name", null)) || empty($info->get("name"))) {
				unset($info);
				continue;
			}
			$this->addEconomy(new Economy($info->get("name"), $info->get("symbol", "$"), $info->get("default", 0)));
			unset($info);
		}
	}

	public function addEconomy(Economy $economy) : void
	{
		$this->listEconomy[$economy->getId()] = $economy;
		Main::getInstance()->getLogger()->info("Economy {$economy->getName()} added");
	}

	public function getEconomy(string $id) : ?Economy
	{
		return $this->listEconomy[$id] ?? null;
	}

	/**
	 * @return Economy[]
	 */
	public function getEconomyList() : array
	{
		return $this->listEconomy;
	}

	public function isEconomy(string $id) : bool
	{
		return isset($this->listEconomy[$id]);
	}

}
