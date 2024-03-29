<?php

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


    protected function prepare(): void
    {
        $this->setPermission("multieconomy.command.add");
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