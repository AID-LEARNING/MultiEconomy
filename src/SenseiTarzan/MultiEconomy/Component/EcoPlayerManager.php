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

use Generator;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SOFe\AwaitGenerator\Await;
use function strtolower;

final class EcoPlayerManager
{

	use SingletonTrait;

	/**
	 * @var EcoPlayer[] $listEcoPlayer
	 * @phpstan-var array<string, EcoPlayer>
	 */
	private array $listEcoPlayer = [];

	public function __construct()
	{
	}

	public function addEcoPlayer(EcoPlayer $ecoPlayer) : Generator
	{
		return Await::promise(function ($resolve) use($ecoPlayer) : void{
			$this->listEcoPlayer[$ecoPlayer->getId()] = $ecoPlayer;
			$resolve();
		});
	}

	public function removeEcoPlayer(Player $player) : void
	{
		unset($this->listEcoPlayer[strtolower($player->getName())]);
	}

	public function getEcoPlayer(Player|string $player) : ?EcoPlayer
	{
		return $this->listEcoPlayer[strtolower($player instanceof Player ? $player->getName() : $player)] ?? null;
	}

}
