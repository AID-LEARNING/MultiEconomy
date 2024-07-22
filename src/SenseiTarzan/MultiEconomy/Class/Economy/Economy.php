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

namespace SenseiTarzan\MultiEconomy\Class\Economy;

use \Throwable;
use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyNoHasAmountException;
use SenseiTarzan\MultiEconomy\Class\Exception\InfiniteValueException;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;
use function is_infinite;
use function is_string;
use function strtolower;

class Economy
{

	private readonly string $id;

	public function __construct(private readonly string $name, private readonly string $symbol, private readonly float $default)
	{
		$this->id = strtolower($name);
	}

	public function getId() : string
	{
		return $this->id;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getDefault() : float
	{
		return $this->default;
	}

	public function getSymbol() : string
	{
		return $this->symbol;
	}

	/**
	 * @return Generator <bool> online or offline
	 */
	public function add(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;
		Main::getInstance()->getLogger()->info("Creation de la promesse de add de " . $name . " de " . $amount . " " . $this->getName());
		return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) : void {
			if (is_infinite($amount)){
				$reject(new InfiniteValueException("Infinite Value"));
				return;
			}
			Await::f2c(function () use ($player, $amount, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}
				if ($player instanceof EcoPlayer){
					$player->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "add", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				if ($player instanceof Player) {
					EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "add", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "add", ["economy" => $this->getId(), "amount" => $amount]);
				return false;

			}, function (bool $value) use ($resolve, $player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de add de " . $name . " de " . $amount . " " . $this->getName() . " terminé");
				$resolve($value);
			},  function (Throwable $throwable) use ($reject,$player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de add de " . $name . " de " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	/**
	 * @return Generator <bool> online or offline receiver
	 */
	public function subtract(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;
		Main::getInstance()->getLogger()->info("Creation de la promesse de substract de " . $name . " pour " . $amount . " " . $this->getName());
		return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) {
			if (is_infinite($amount)){
				$reject(new InfiniteValueException("Infinite Value"));
				return;
			}
			Await::f2c(function () use ($player, $amount, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}
				if ($player instanceof EcoPlayer){
					$player->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "subtract", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				if ($player instanceof Player) {
					EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "subtract", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "subtract", ["economy" => $this->getId(), "amount" => $amount]);
				return false;
			}, function (bool $value) use ($resolve, $player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de subtract de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
				$resolve($value);
			},  function (Throwable $throwable) use ($reject,$player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de subtract de " . $name . " pour " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	/**
	 * @return Generator <bool> online or offline receiver
	 */
	public function set(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;
		Main::getInstance()->getLogger()->info("Creation de la promesse de set de " . $name . " pour " . $amount . " " . $this->getName());
	return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) {
		if (is_infinite($amount)){
			$reject(new InfiniteValueException("Infinite Value"));
			return;
		}
			Await::f2c(function () use ($player, $amount, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}
				if ($player instanceof EcoPlayer){
					$player->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($name, "set", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				if ($player instanceof Player) {
					EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($name, "set", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				yield from DataManager::getInstance()->getDataSystem()->updateOffline($name, "set", ["economy" => $this->getId(), "amount" => $amount]);
				return false;
			}, function (bool $value) use ($resolve, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de set de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
				$resolve($value);
			},  function (Throwable $throwable) use ($reject,$amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de set de " . $name . " pour " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	/**
	 * @return Generator <bool> online or offline
	 */
	public function multiply(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;
		Main::getInstance()->getLogger()->info("Creation de la promesse de multiply de " . $name . " de " . $amount . " " . $this->getName());
		return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) : void {
			if (is_infinite($amount)){
				$reject(new InfiniteValueException("Infinite Value"));
				return;
			}
			Await::f2c(function () use ($player, $amount, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}
				if ($player instanceof EcoPlayer) {
					$player->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "multiply", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				if ($player instanceof Player) {
					EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "multiply", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "multiply", ["economy" => $this->getId(), "amount" => $amount]);
				return false;

			}, function (bool $value) use ($resolve, $player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de multiply de " . $name . " de " . $amount . " " . $this->getName() . " terminé");
				$resolve($value);
			},  function (Throwable $throwable) use ($reject,$player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de multiply de " . $name . " de " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	/**
	 * @param Player|string $player
	 * @return Generator <bool> online or offline
	 */
	public function division(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;
		Main::getInstance()->getLogger()->info("Creation de la promesse de division de " . $name . " de " . $amount . " " . $this->getName());
		return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) : void {
			if (is_infinite($amount)){
				$reject(new InfiniteValueException("Infinite Value"));
				return;
			}
			Await::f2c(function () use ($player, $amount, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}
				if ($player instanceof EcoPlayer) {
					$player->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "division", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				if ($player instanceof Player) {
					EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "division", ["economy" => $this->getId(), "amount" => $amount]));
					return true;
				}
				yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "division", ["economy" => $this->getId(), "amount" => $amount]);
				return false;

			}, function (bool $value) use ($resolve, $player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de division de " . $name . " de " . $amount . " " . $this->getName() . " terminé");
				$resolve($value);
			},  function (Throwable $throwable) use ($reject,$player, $amount, $name) {
				Main::getInstance()->getLogger()->info("Promesse de division de " . $name . " de " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	/**
	 * @return Generator <bool> online or offline
	 */
	public function percent(Player|string $player, float $amount) : Generator
	{
		return $this->multiply($player, $amount / 100);
	}

	public function get(Player|string $player) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($player) : void{
			$data = DataManager::getInstance()->getDataSystem();
			if ($data === null){
				$resolve($this->getDefault());
				return;
			}
			Await::g2c($data->createPromiseGetBalance($player, $this->getId()), $resolve, $reject);
		});
	}

	/**
	 * @return Generator <bool> online or offline receiver
	 */
	public function pay(Player|string $sender, Player|string $receiver, float $amount) : Generator
	{
		Main::getInstance()->getLogger()->info("Creation de la promesse de pay de " . ($sender instanceof Player ? $sender->getName() : $sender) . " vers " . ($receiver instanceof Player ? $receiver->getName() : $receiver) . " pour " . $amount . " " . $this->getName());
		return Await::promise(function ($resolve, $reject) use ($sender, $receiver, $amount) {
			Await::f2c(function () use ($sender, $receiver, $amount) : Generator {
				yield from $this->has($sender, $amount);
				yield from $this->subtract($sender, $amount);
				return yield from $this->add($receiver, $amount);
			}, function (bool $result) use ($resolve, $sender, $receiver, $amount) {
				Main::getInstance()->getLogger()->info("Promesse de pay de " . ($sender instanceof Player ? $sender->getName() : $sender) . " vers " . ($receiver instanceof Player ? $receiver->getName() : $receiver) . " pour " . $amount . " " . $this->getName() . " terminé");
				$resolve($result);
			}, function (Throwable $throwable) use ($reject, $sender, $receiver, $amount){
				Main::getInstance()->getLogger()->info("Promesse de pay de " . ($sender instanceof Player ? $sender->getName() : $sender) . " vers " . ($receiver instanceof Player ? $receiver->getName() : $receiver) . " pour " . $amount . " " . $this->getName() . " échoué");
				$reject($throwable);
			});
		});
	}

	public function has(Player|string $player, float $amount) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($player, $amount) {
			Await::g2c($this->get($player), function ($result) use ($player, $resolve, $reject, $amount) {
				if ($result >= $amount) {
					$resolve();
					return;
				}
				$reject(new EconomyNoHasAmountException($player instanceof Player ? $player->getName() : $player));
			});
		});
	}
}
