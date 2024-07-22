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

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\BaseSubCommand;
use pmmp\thread\ThreadSafeArray;
use pocketmine\command\CommandSender;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SenseiTarzan\MultiEconomy\Utils\Format;
use SOFe\AwaitGenerator\Await;
use Throwable;

class topBalanceSubCommand extends BaseSubCommand
{

	protected function prepare() : void
	{
		$this->setPermission("multieconomy.command.top");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
	{
		/**
		 * @var EconomyCommand $parent
		 */
		$parent = $this->getParent();
		$economy = $parent->getSymbole();
		$id = $parent->getName();
		Await::g2c(DataManager::getInstance()->getDataSystem()->createPromiseTop($id, 10), function (ThreadSafeArray $result) use ($sender, $id, $economy) {
			$index = 0;
			$text = LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::header_economy_top(10, MultiEconomyManager::getInstance()->getEconomy($id)->getName())) . "\n";
			foreach (Format::threadSafeArrayToArray($result) as $name => $amounts) {
				$text .= LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::body_economy_top(++$index, $name, $amounts, $economy)) . "\n";
			}
			$sender->sendMessage($text);
		}, function (Throwable $exception) {
			Main::getInstance()->getLogger()->logException($exception);
		});
	}
}
