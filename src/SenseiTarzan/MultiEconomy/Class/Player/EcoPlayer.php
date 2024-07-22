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
use SenseiTarzan\MultiEconomy\Events\EcolPlayerLoadedEvent;
use SenseiTarzan\MultiEconomy\Events\EconomyChangeDataEvent;
use function strtolower;

class EcoPlayer implements JsonSerializable
{

	private string $id;

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
		return $this->economy[$id] ?? 0.0;
	}

	/**
	 * @return array<string, float>
	 */
	public function getEconomies() : array
	{
		return $this->economy;
	}

	public function setEconomy(string $id, float $amount) : void
	{
		$this->economy[$id] = $amount;
		if (EconomyChangeDataEvent::hasHandlers()) {
			$event = new EconomyChangeDataEvent($this->player, $id, $amount);
			$event->call();
		}
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
