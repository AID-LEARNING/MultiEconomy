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

use Error;
use Exception;
use Generator;
use pocketmine\player\Player;
use SenseiTarzan\DataBase\Class\IDataSave;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SOFe\AwaitGenerator\Await;

abstract class IDataSaveEconomy implements IDataSave
{

	const SUBTRACT = "subtract";
	const SET = "set";
	const ADD = "add";
	private const PAY = "pay";

	final public function loadDataPlayer(Player|string $player) : void
	{
		Await::f2c(function () use ($player) : Generator{
			$data = yield from $this->createPromiseEconomy($player);
			return (yield from EcoPlayerManager::getInstance()->addEcoPlayer(new EcoPlayer($player, $data)));
		}, null, function (Exception|Error $throwable) use ($player){
			if ($player instanceof Player){
				$player->kick($throwable->getMessage());
			}
		});
	}

	public function loadDataPlayerByMiddleware(Player|string $player) : Generator
	{
		return Await::promise(function ($resolve) use ($player){
			Await::f2c(function () use ($player){
				$data = yield from $this->createPromiseEconomy($player);
				try {
					return EcoPlayerManager::getInstance()->addEcoPlayer(new EcoPlayer($player, $data));
				} catch (Error|Exception $throwable){
					return $throwable;
				}
			}, $resolve);
		});
	}

	abstract protected function createPromiseAllBalance(Player|string $player) : Generator;

	abstract public function createPromiseEconomy(Player|string $player) : Generator;

	/**
	 * @return Generator<float>
	 */
	abstract public function createPromiseGetBalance(Player|string $player, string $economy) : Generator;

	final public function updateOnline(string $id, string $type, mixed $data) : Generator
	{
		return $this->createPromiseUpdate($id, $type, $data);
	}

	/**
	 * @inheritDoc
	 */
	final public function updateOffline(string $id, string $type, mixed $data) : Generator
	{
		return $this->createPromiseUpdate($id, $type, $data);
	}

	abstract public function createPromiseUpdate(string $id, string $type, mixed $data) : Generator;

	abstract function createPromiseTop(string $economy, int $limite) : Generator;

}
