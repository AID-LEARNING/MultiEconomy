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

namespace SenseiTarzan\MultiEconomy\Class\Player;

use JsonSerializable;
use pocketmine\player\Player;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Events\EcolPlayerLoadedEvent;
use SenseiTarzan\MultiEconomy\Events\EconomyChangeDataEvent;
use function strtolower;
use const PHP_INT_MAX;

class EcoPlayer implements JsonSerializable
{

	private string $id;

	/**
	 * @param int[] $economy Array of economy id and amount in cents
	 */
	public function __construct(private readonly Player $player, private array $economy)
	{
		$this->id = strtolower($this->player->getName());
		if (EcolPlayerLoadedEvent::hasHandlers()) {
			$event = new EcolPlayerLoadedEvent($this->player, $this);
			$event->call();
		}
	}

	public function getId() : string
	{
		return $this->id;
	}

	public function getName() : string
	{
		return $this->player->getName();
	}

	/**
	 * @internal
	 */
	public function getEconomy(string $id) : float
	{
		$economy = MultiEconomyManager::getInstance()->getEconomy($id);
		return $economy->centToUnit($this->economy[$id] ?? 0);
	}

	public function getEconomyInCent(string $id) : int
	{
		return$this->economy[$id] ?? 0;
	}

	/**
	 * @return array<string, float>
	 */
	public function getEconomies() : array
	{
		return $this->economy;
	}

	/**
	 * @internal Set economy amount in cents
	 */
	public function setEconomy(string $id, int $amount) : void
	{
		$this->economy[$id] = $amount;
		if (EconomyChangeDataEvent::hasHandlers()) {
			$event = new EconomyChangeDataEvent($this->player, $id, $amount);
			$event->call();
		}
	}

	/**
	 * @internal Add economy amount in cents
	 */
	public function addEconomy(string $id,int $amount) : void
	{
		if ($this->economy[$id] >= PHP_INT_MAX) {
			return;
		}
		$this->economy[$id] += $amount;
	}

	/**
	 * @internal Subtract economy amount in cents
	 */
	public function subtractEconomy(string $id, int $amount) : void {
		$this->economy[$id] -= $amount;
	}

	/**
	 * @internal Multiply economy amount in cents
	 */
	public function multiplyEconomy(string $id, int $amount) : void
	{
		if ($this->economy[$id] >= PHP_INT_MAX) {
			return;
		}
		$this->economy[$id] *= $amount;
	}

	/**
	 * @internal Divide economy amount in cents
	 */
	public function divideEconomy(string $id, int $amount) : void {
		if($amount === 0) {
			return;
		}
		$this->economy[$id] /= $amount;
	}

	public function existsEconomy(string $id) : bool
	{
		return isset($this->economy[$id]);
	}

	public function jsonSerialize() : array
	{
		return $this->economy;
	}
}
