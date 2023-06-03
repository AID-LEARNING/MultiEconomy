<?php

namespace SenseiTarzan\MultiEconomy;

use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\ExtraEvent\Component\EventLoader;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Save\YAMLSave;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Listener\PlayerListener;
use SenseiTarzan\Path\PathScanner;
use SOFe\AwaitGenerator\Await;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{

    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
        if (!file_exists(Path::join($this->getDataFolder(), "config.yml"))) {
            foreach (PathScanner::scanDirectoryGenerator($search =  Path::join(dirname(__DIR__,3) , "resources")) as $file){
                @$this->saveResource(str_replace($search, "", $file));
            }
        }
        DataManager::getInstance()->setDataSystem(new YAMLSave($this));
        new MultiEconomyManager($this);
        new LanguageManager($this);
    }


    public function onEnable(): void
    {
        EventLoader::loadEventWithClass($this, PlayerListener::class);

        if (!PacketHooker::isRegistered()){
            PacketHooker::register($this);
        }
        LanguageManager::getInstance()->loadCommands("economy");
        foreach (MultiEconomyManager::getInstance()->getEconomyList() as $economy) {
           $this->getServer()->getCommandMap()->register("multieconomy", new EconomyCommand($this, $economy->getId(), $economy->getSymbol(), "{$economy->getName()} command"));
        }
    }
}