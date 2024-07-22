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

namespace SenseiTarzan\MultiEconomy\Class\Save;

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Task\AsyncSortTask;
use SOFe\AwaitGenerator\Await;
use Throwable;
use function strtolower;

final class YAMLSave extends IDataSaveEconomy
{
	private Config $data;

	public function __construct(Main $plugin)
	{
		$this->data = new Config($plugin->getDataFolder() . "data.yml", Config::YAML);
	}

	public function getName() : string
	{
		return "YAML";
	}

	public function createPromiseEconomy(Player|string $player) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($player) {
			Await::f2c(function () use($player) : Generator{
				yield from $this->createPromiseAllBalance($player);
				return ($this->data->get(strtolower($player instanceof Player ? $player->getName() : $player), []));
			}, $resolve, $reject);
		});
	}

	protected function createPromiseAllBalance(Player|string $player) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($player) : void {
			try {
				foreach (MultiEconomyManager::getInstance()->getEconomyList() as $economy){
					$search = ($player instanceof Player ? $player->getName() : $player) . "." . $economy->getId();
					$this->data->setNested($search, $this->data->getNested($search, $economy->getDefault()));
				}
				$this->data->save();
				$resolve();
			} catch (Throwable) {
				$reject(new EconomyUpdateException("Error Creation Blance of All Economy"));
			}
		});
	}

	public function createPromiseGetBalance(Player|string $player, string $economy) : Generator
	{
		return Await::promise(function ($resolve) use ($player, $economy) {
			$resolve(EcoPlayerManager::getInstance()->getEcoPlayer($player)?->getEconomy($economy) ?? $this->data->getNested(strtolower(($player instanceof Player ? $player->getName() : $player) . ".$economy"), 0));
		});
	}

	public function createPromiseUpdate(string $id, string $type, mixed $data) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($id, $type, $data) {
			try {
				$economyType = strtolower($data["economy"]);
				$balance = $data["amount"];
				switch (strtolower($type)) {
					case "add":
					{
						$balance = $this->data->getNested($id . ".$economyType");
						$balance += $data["amount"];
						break;
					}
					case "subtract":
					{
						$balance = $this->data->getNested($id . ".$economyType");
						$balance -= $data["amount"];
						if ($balance < 0) {
							$balance = 0;
						}
						break;
					}
					case "multiply":
					{
						$balance = $this->data->getNested($id . ".$economyType");
						$balance *= $data["amount"];
						break;
					}
					case "division":
					{
						$balance = $this->data->getNested($id . ".$economyType");
						if ($data["amount"] === 0)
						{
							$reject(new \InvalidArgumentException("Dont you cant divided with zero"));
							return ;
						}
						$balance /= $data["amount"];
						break;
					}
				}
				$this->data->setNested($id . ".$economyType", $balance);
				$this->data->save();
				$resolve($balance);
			} catch (Throwable) {
				$reject(new EconomyUpdateException("Error updating economy $economyType for $id with $type " . $data["amount"]));
			}
		});
	}

	public function createPromiseTop(string $economy, int $limite = 10) : Generator
	{
		return Await::promise(function ($resolve) use ($economy, $limite) {
			Server::getInstance()->getAsyncPool()->submitTask(new AsyncSortTask($economy, $limite, $this->data->getAll(), $resolve));
		});
	}
}
