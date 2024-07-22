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

namespace SenseiTarzan\MultiEconomy;

use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\ExtraEvent\Component\EventLoader;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\Middleware\Component\MiddlewareManager;
use SenseiTarzan\MultiEconomy\Class\Middleware\EcoMiddleWare;
use SenseiTarzan\MultiEconomy\Class\Save\JSONSave;
use SenseiTarzan\MultiEconomy\Class\Save\YAMLSave;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Listener\PlayerListener;
use SenseiTarzan\Path\PathScanner;
use Symfony\Component\Filesystem\Path;
use function dirname;
use function file_exists;
use function mb_strtolower;
use function str_replace;

class Main extends PluginBase
{

	use SingletonTrait;

	protected function onLoad() : void
	{
		self::setInstance($this);
		if (!file_exists(Path::join($this->getDataFolder(), "config.yml"))) {
			foreach (PathScanner::scanDirectoryGenerator($search = Path::join(dirname(__DIR__, 3), "resources")) as $file) {
				@$this->saveResource(str_replace($search, "", $file));
			}
		}
		DataManager::getInstance()->setDataSystem(match (mb_strtolower($this->getConfig()->get("data-type", "yml"))) {
			"yml", "yaml" => new YAMLSave($this),
			"json" => new JSONSave($this),
			default => null
		});
		new MultiEconomyManager($this);
		new LanguageManager($this);
	}

	public function onEnable() : void
	{
		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}
		$hasMiddleware = Server::getInstance()->getPluginManager()->getPlugin("Middleware") !== null;
		if ($hasMiddleware)
			MiddlewareManager::getInstance()->addMiddleware(new EcoMiddleWare());
		EventLoader::loadEventWithClass($this, new PlayerListener($hasMiddleware));
		foreach (MultiEconomyManager::getInstance()->getEconomyList() as $economy) {
			$this->getServer()->getCommandMap()->register("multieconomy", new EconomyCommand($this, $economy->getId(), $economy->getSymbol(), "{$economy->getName()} command"));
		}
		LanguageManager::getInstance()->loadCommands("economy");
	}
}
