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
use SenseiTarzan\MultiEconomy\Class\Economy\Economy;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\subCommand\addBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\payBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\setBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\subtractBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Commands\subCommand\topBalanceSubCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class EconomyCommand extends BaseCommand
{

	public function __construct(PluginBase $plugin, string $name, private readonly Economy $economy, string $description = "", array $aliases = [])
	{
		parent::__construct($plugin, $name, $description, $aliases);
	}

	public function getSymbole() : string
	{
		return $this->economy->getSymbol();
	}

	/**
	 * @inheritDoc
	 */
	protected function prepare() : void
	{
		$this->setPermission("multieconomy.command");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerArgument(0, new PlayerArgument(true, "player"));
		if($this->economy->isEnablePay()) {
			$this->registerSubCommand(new payBalanceSubCommand($this->getOwningPlugin(), "pay", $this->economy, "Payer un joueur", ["send", "donate"]));
		}
		$this->registerSubCommand(new addBalanceSubCommand($this->getOwningPlugin(), "add", $this->economy, "Ajouter de l'argent à un joueur"));
		$this->registerSubCommand(new subtractBalanceSubCommand($this->getOwningPlugin(), "subtract", $this->economy, "Soustraire de l'argent à un joueur", ["sub", "remove"]));
		$this->registerSubCommand(new setBalanceSubCommand($this->getOwningPlugin(), "set", $this->economy, "Définir le solde d'un joueur"));
		$this->registerSubCommand(new topBalanceSubCommand($this->getOwningPlugin(), "top", $this->economy, "Afficher le top des joueurs"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void
	{
		if (empty($args)) {
			Await::g2c(MultiEconomyManager::getInstance()->getEconomy($this->getName())->get($sender), function (float $balance) use ($sender) {
				$sender->sendMessage(Main::getInstance()->getLanguageManager()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::balance_economy_sender($this->getSymbole(), $balance)));
			});
		}elseif($sender->hasPermission("multieconomy.command.see")){
			Await::g2c(MultiEconomyManager::getInstance()->getEconomy($this->getName())->get($args["player"]), function (float $balance) use ($sender) {
				$sender->sendMessage(Main::getInstance()->getLanguageManager()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::balance_economy_sender($this->getSymbole(), $balance)));
			});
		}
	}
}
