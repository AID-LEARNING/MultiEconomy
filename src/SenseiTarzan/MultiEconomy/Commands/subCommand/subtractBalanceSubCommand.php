<?php

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class subtractBalanceSubCommand extends BaseSubCommand
{


    protected function prepare(): void
    {
        $this->setPermission("multieconomy.command.subtract");
        $this->registerArgument(0, new PlayerArgument(name: "player"));
        $this->registerArgument(1, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /**
         * @var EconomyCommand $parent
         */
        $parent = $this->getParent();
        $target = $args["player"];
        $amount = $args["amount"];
        $economy = $parent->getSymbole();
        $id = $parent->getName();
        Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->subtract($target, $amount),
            function (bool $online) use ($target, $amount, $economy, $sender) {
                $sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::subtract_economy_sender($target, $economy, $amount)));
                if (!$online) return;
                $target->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($target, CustomKnownTranslationFactory::subtract_economy_receiver($economy, $amount)));
            }, [
                EconomyUpdateException::class => function (EconomyUpdateException $exception) use ($sender) {
                    Main::getInstance()->getLogger()->logException($exception);
                }
            ]);
    }
}