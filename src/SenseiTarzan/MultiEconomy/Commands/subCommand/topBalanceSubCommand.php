<?php

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\BaseSubCommand;
use Exception;
use pocketmine\command\CommandSender;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;

class topBalanceSubCommand extends BaseSubCommand
{


    protected function prepare(): void
    {
        $this->setPermission("multieconomy.command.top");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /**
         * @var EconomyCommand $parent
         */
        $parent = $this->getParent();
        $economy = $parent->getSymbole();
        $id = $parent->getName();
        Await::g2c(DataManager::getInstance()->getDataSystem()->createPromiseTop($id, 10), function (array $result) use ($sender, $id, $economy) {
            $sender->sendMessage(LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::header_economy_top(10, MultiEconomyManager::getInstance()->getEconomy($id)->getName())));
            $index = 0;
            $text = "";
            foreach ($result as $name => $amounts) {
                $text .= LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::body_economy_top($index++, $name, $amounts, $economy)) . "\n";
            }
            $sender->sendMessage($text);
        }, function ()  use ($sender){
            Main::getInstance()->getLogger()->logException(new Exception("Error when get top balance"));
        });
    }
}