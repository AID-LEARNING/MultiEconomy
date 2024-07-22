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

namespace SenseiTarzan\MultiEconomy\Listener;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\ExtraEvent\Class\EventAttribute;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;

final class PlayerListener
{
	public function __construct(private readonly bool $hasMiddleware)
	{
	}

	#[EventAttribute(EventPriority::LOW)]
	public function onJoin(PlayerJoinEvent $event) : void
	{
		if (!$this->hasMiddleware)
			DataManager::getInstance()->getDataSystem()->loadDataPlayer($event->getPlayer());
	}

	#[EventAttribute(EventPriority::LOW)]
	public function onQuit(PlayerQuitEvent $event) : void
	{
		EcoPlayerManager::getInstance()->removeEcoPlayer($event->getPlayer());
	}

}
