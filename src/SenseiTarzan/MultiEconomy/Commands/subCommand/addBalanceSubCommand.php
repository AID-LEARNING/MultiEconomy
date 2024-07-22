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

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Class\Exception\InfiniteValueException;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class addBalanceSubCommand extends BaseSubCommand
{

	protected function prepare() : void
	{
		$this->setPermission("multieconomy.command.add");
		$this->registerArgument(0, new PlayerArgument(name: "player"));
		$this->registerArgument(1, new FloatArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
	{
		/**
		 * @var EconomyCommand $parent
		 */
		$parent = $this->getParent();
		$player = $args["player"];
		$amount = $args["amount"];
		$economy = $parent->getSymbole();
		$id = $parent->getName();
		Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->add($player, $amount),
			function (bool $online) use ($player, $amount, $economy, $sender) {
				$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::add_economy_sender($player, $economy, $amount)));
				if (!$online) return;
				$player->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($player, CustomKnownTranslationFactory::add_economy_receiver($economy, $amount)));
			}, [
				EconomyUpdateException::class => function (EconomyUpdateException $exception) use ($sender) {
					Main::getInstance()->getLogger()->logException($exception);
				},
				InfiniteValueException::class => function (){}
			]);
	}
}
