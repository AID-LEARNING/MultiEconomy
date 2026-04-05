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

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyNoHasAmountException;
use SenseiTarzan\MultiEconomy\Class\Exception\InfiniteValueException;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;
use Throwable;
use function is_infinite;
use function is_string;
use function round;
use function strtolower;

class Economy
{

	private readonly string $id;

	private readonly int $default;

	public function __construct(private readonly string $name,
								private readonly string $symbol,
								float $default,
								private readonly int $centToUnit = 100,
								private readonly bool $enablePay = true
	)
	{
		$this->id = strtolower($name);
		$this->default = $this->convertToCent($default);
	}

	public function getId() : string
	{
		return $this->id;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getDefault() : int
	{
		return $this->default;
	}

	public function getSymbol() : string
	{
		return $this->symbol;
	}

	public function getCentToUnit() : int
	{
		return $this->centToUnit;
	}

	public function convertToCent(float $amount) : int
	{
		return (int) round($amount * $this->centToUnit);
	}

	public function centToUnit(int $amount) : float
	{
		return round($amount / $this->centToUnit, 2);
	}

	public function isEnablePay() : bool
	{
		return $this->enablePay;
	}

	private function processTransaction(string $action, EcoPlayer|Player|string $player, float $amount) : Generator
	{
		$name = $player instanceof Player ? $player->getName() : $player;

		Main::getInstance()->getLogger()->info("Création de la promesse de $action de $name pour $amount " . $this->getName());
		$amount = $this->convertToCent($amount);
		return Await::promise(function ($resolve, $reject) use ($player, $amount, $name, $action) : void {
			if (is_infinite($amount)) {
				$reject(new InfiniteValueException("Infinite Value"));
				return;
			}

			Await::f2c(function () use ($player, $amount, $action, $name) {
				if (is_string($player)) {
					$player = Server::getInstance()->getPlayerExact($player) ?? $player;
				}

				$data = ["economy" => $this->getId(), "amount" => $amount];

				if ($player instanceof EcoPlayer) {
					match ($action) {
						"add" => $player->addEconomy($this->getId(), $amount),
						"subtract" => $player->subtractEconomy($this->getId(), $amount),
						"set" => $player->setEconomy($this->getId(), $amount),
						"multiply" => $player->multiplyEconomy($this->getId(), $amount),
						"divide" => $player->divideEconomy($this->getId(), $amount),
						default => null
					};
					$result = yield from Main::getInstance()->getDataManager()->getDataSystem()->updateOnline($player->getName(), $action, $data);
					$player->setEconomy($this->getId(), $result);
					return true;
				}

				if ($player instanceof Player) {
					$ecoPlayer = EcoPlayerManager::getInstance()->getEcoPlayer($player);
					if ($ecoPlayer !== null) {
						match ($action) {
							"add" => $ecoPlayer->addEconomy($this->getId(), $amount),
							"subtract" => $ecoPlayer->subtractEconomy($this->getId(), $amount),
							"set" => $ecoPlayer->setEconomy($this->getId(), $amount),
							"multiply" => $ecoPlayer->multiplyEconomy($this->getId(), $amount),
							"divide" => $ecoPlayer->divideEconomy($this->getId(), $amount),
							default => null
						};
						$result = yield from Main::getInstance()->getDataManager()->getDataSystem()->updateOnline($player->getName(), $action, $data);
						$ecoPlayer->setEconomy($this->getId(), $result);
						return true;
					}
				}

				yield from Main::getInstance()->getDataManager()->getDataSystem()->updateOffline($name, $action, $data);
				return false;

			},
				function (bool $value) use ($resolve, $name, $amount, $action) {
					Main::getInstance()->getLogger()->info("Promesse de $action de $name pour $amount " . $this->getName() . " terminé");
					$resolve($value);
				},
				function (Throwable $throwable) use ($reject, $name, $amount, $action) {
					Main::getInstance()->getLogger()->info("Promesse de $action de $name pour $amount " . $this->getName() . " échoué");
					$reject($throwable);
				});
		});
	}

	public function add(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		return $this->processTransaction("add", $player, $amount);
	}

	public function subtract(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		return $this->processTransaction("subtract", $player, $amount);
	}

	public function set(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		return $this->processTransaction("set", $player, $amount);
	}

	public function multiply(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		return $this->processTransaction("multiply", $player, $amount);
	}

	public function division(EcoPlayer|Player|string $player, float $amount) : Generator
	{
		return $this->processTransaction("division", $player, $amount);
	}

	/**
	 * @return Generator <bool> online or offline
	 */
	public function percent(Player|string $player, float $amount) : Generator
	{
		return $this->multiply($player, $amount / 100);
	}

	public function get(Player|string $player, bool $cache = true) : Generator
	{
		return Await::promise(function ($resolve, $reject) use ($cache, $player) : void{
			$data = Main::getInstance()->getDataManager()->getDataSystem();
			if ($data === null){
				$resolve($this->getDefault());
				return;
			}
			Await::g2c($data->createPromiseGetBalance($player, $this->getId(), $cache), $resolve, $reject);
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
				if (is_string($sender)) {
					$sender = Server::getInstance()->getPlayerExact($sender) ?? $sender;
				}
				if (is_string($receiver)) {
					$receiver = Server::getInstance()->getPlayerExact($receiver) ?? $receiver;
				}
				$data = yield from Main::getInstance()->getDataManager()->getDataSystem()->createPromiseUpdate(is_string($sender) ? $sender : $sender->getName(), "pay", ["economy" => $this->getId(), "amount" => $amount, "default" => $this->getDefault(), "receiver" => is_string($receiver) ? $receiver : $receiver->getName()]);
				if ($sender instanceof Player)
					EcoPlayerManager::getInstance()->getEcoPlayer($sender)->setEconomy($this->getId(), $data["sender"]);
				if ($receiver instanceof Player)
					EcoPlayerManager::getInstance()->getEcoPlayer($receiver)->setEconomy($this->getId(), $data["receiver"]);
				return !is_string($receiver) && $receiver->isConnected();
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
