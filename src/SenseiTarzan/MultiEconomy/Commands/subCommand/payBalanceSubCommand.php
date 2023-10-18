<?php

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyNoHasAmountException;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Commands\args\PlayerArgument;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class payBalanceSubCommand extends BaseSubCommand
{


    protected function prepare(): void
    {
        $this->setPermission("multieconomy.command.add");
        $this->registerArgument(0, new PlayerArgument(name: "player"));
        $this->registerArgument(1, new FloatArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
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
                }
            ]);
    }
}