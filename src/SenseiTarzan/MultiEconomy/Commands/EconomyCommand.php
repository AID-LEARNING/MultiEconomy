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

namespace SenseiTarzan\MultiEconomy\Commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Commands\subCommand\addBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\payBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\setBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\subtractBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\topBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class EconomyCommand extends BaseCommand
{

	public function __construct(PluginBase $plugin, string $name, private readonly string $symbole, string $description = "", array $aliases = [])
	{
		parent::__construct($plugin, $name, $description, $aliases);
	}

	public function getSymbole() : string
	{
		return $this->symbole;
	}

	/**
	 * @inheritDoc
	 */
	protected function prepare() : void
	{
		$this->setPermission("multieconomy.command");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new payBalanceSubCommand($this->getOwningPlugin(), "pay", "Payer un joueur", ["send", "donate"]));
		$this->registerSubCommand(new addBalanceSubCommand($this->getOwningPlugin(), "add", "Ajouter de l'argent à un joueur"));
		$this->registerSubCommand(new subtractBalanceSubCommand($this->getOwningPlugin(), "subtract", "Soustraire de l'argent à un joueur", ["sub", "remove"]));
		$this->registerSubCommand(new setBalanceSubCommand($this->getOwningPlugin(), "set", "Définir le solde d'un joueur"));
		$this->registerSubCommand(new topBalanceSubCommand($this->getOwningPlugin(), "top", "Afficher le top des joueurs"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
	{
		Await::g2c(MultiEconomyManager::getInstance()->getEconomy($this->getName())->get($sender), function (float $balance) use ($sender) {
			$sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::balance_economy_sender($this->getSymbole(), $balance)));
		});
	}
}
