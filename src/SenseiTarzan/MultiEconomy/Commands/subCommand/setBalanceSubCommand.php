<?php

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\args\TargetPlayerArgument;
use CortexPE\Commando\BaseSubCommand;
use Exception;
use pocketmine\command\CommandSender;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class setBalanceSubCommand extends BaseSubCommand
{


    protected function prepare(): void
    {
        $this->setPermission("multieconomy.command.set");
        $this->registerArgument(0, new PlayerArgument(name: "player"));
        $this->registerArgument(1, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /**
         * @var EconomyCommand $parent
         */
        $parent = $this->getParent();
        $player = $args["player"];
        $amount = $args["amount"];
        $economy = $parent->getSymbole();
        $id = $parent->getName();
        Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->set($player, $amount),
            function (bool $online) use ($player, $amount, $economy, $sender) {
                $sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::set_economy_sender($player, $economy, $amount)));
                if (!$online) return;
                $player->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($player, CustomKnownTranslationFactory::set_economy_receiver($economy, $amount)));
            }, function (Exception $exception) use ($economy) {
                if ($exception->getCode() === 417) {
                    Main::getInstance()->getLogger()->error("Erreur pendant la sauvegarde des données de l'économie: " . $economy);
                }
            });
    }
}