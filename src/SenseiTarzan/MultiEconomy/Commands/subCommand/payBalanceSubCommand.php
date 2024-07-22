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
use pocketmine\player\Player;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyNoHasAmountException;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Class\Exception\InfiniteValueException;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;
use function is_string;

class payBalanceSubCommand extends BaseSubCommand
{

	protected function prepare() : void
	{
		$this->setPermission("multieconomy.command.pay");
		$this->registerArgument(0, new PlayerArgument(name: "player"));
		$this->registerArgument(1, new FloatArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
	{
		$target = $args["player"];
		if (is_string($target)) {
			$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::error_target_not_online($target)));
			return;
		}
		if ($target->getName() === $sender->getName()) {
			$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::error_target_yourself()));
			return;
		}
		/**
		 * @var EconomyCommand $parent
		 */
		$parent = $this->getParent();
		$amount = $args["amount"];
		if ($amount < 0) {
			$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::error_negative_amount()));
			return;
		}
		$id = $parent->getName();
		$economy = $parent->getSymbole();
		/**
		 * @var Player $sender
		 */
		Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->pay($sender, $target, $amount),
			function (bool $online) use ($target, $economy, $amount, $sender) {
				$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::pay_economy_sender($target, $economy, $amount)));
				if (!$online) return;
				$target->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($target, CustomKnownTranslationFactory::pay_economy_receiver($sender, $economy, $amount)));
			}, [
				EconomyNoHasAmountException::class => function () use ($sender) {
					$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::error_not_enough_money()));
				},
				EconomyUpdateException::class => function (EconomyUpdateException $exception) use ($sender) {
					Main::getInstance()->getLogger()->logException($exception);
				},
				InfiniteValueException::class => function (){}
			]);
	}
}
