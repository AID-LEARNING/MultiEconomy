<?php

namespace SenseiTarzan\MultiEconomy\Commands\subCommand;

use CortexPE\Commando\BaseSubCommand;
use Exception;
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
        Await::g2c(DataManager::getInstance()->getDataSystem()->createPromiseTop($id, 10), function (ThreadSafeArray $result) use ($sender, $id, $economy) {
            $index = 0;
            $text = LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::header_economy_top(10, MultiEconomyManager::getInstance()->getEconomy($id)->getName())) . "\n";
            foreach (Format::threadSafeArrayToArray($result) as $name => $amounts) {
                $text .= LanguageManager::getInstance()->getTranslateWithTranslatable($sender, CustomKnownTranslationFactory::body_economy_top(++$index, $name, $amounts, $economy)) . "\n";
            }
            $sender->sendMessage($text);
        }, function (Throwable $exception){
            Main::getInstance()->getLogger()->logException($exception);
        });
    }
}