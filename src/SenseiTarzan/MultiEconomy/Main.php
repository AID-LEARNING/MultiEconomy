<?php

namespace SenseiTarzan\MultiEconomy;

use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\ExtraEvent\Component\EventLoader;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Save\JSONSave;
use SenseiTarzan\MultiEconomy\Class\Save\YAMLSave;
use SenseiTarzan\MultiEconomy\Commands\EconomyCommand;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Listener\PlayerListener;
use SenseiTarzan\Path\PathScanner;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{

    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
        if (!file_exists(Path::join($this->getDataFolder(), "config.yml"))) {
            foreach (PathScanner::scanDirectoryGenerator($search = Path::join(dirname(__DIR__, 3), "resources")) as $file) {
                @$this->saveResource(str_replace($search, "", $file));
            }
        }
        DataManager::getInstance()->setDataSystem(match (mb_strtolower($this->getConfig()->get("data-type", "yml"))) {
            "yml", "yaml" => new YAMLSave($this),
            "json" => new JSONSave($this),
            default => null
        });
        new MultiEconomyManager($this);
        new LanguageManager($this);
    }


    public function onEnable(): void
    {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        EventLoader::loadEventWithClass($this, PlayerListener::class);
        foreach (MultiEconomyManager::getInstance()->getEconomyList() as $economy) {
            $this->getServer()->getCommandMap()->register("multieconomy", new EconomyCommand($this, $economy->getId(), $economy->getSymbol(), "{$economy->getName()} command"));
        }
        LanguageManager::getInstance()->loadCommands("economy");
    }
}