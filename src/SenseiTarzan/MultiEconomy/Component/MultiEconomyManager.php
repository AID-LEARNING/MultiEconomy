<?php

namespace SenseiTarzan\MultiEconomy\Component;

use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Economy\Economy;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\Path\PathScanner;
use Symfony\Component\Filesystem\Path;

final class MultiEconomyManager
{

    use SingletonTrait;

    /**
     * @var Economy[] $listEconomy
     */
    private array $listEconomy = [];

    public function __construct(Main $plugin)
    {
        self::setInstance($this);
        foreach (PathScanner::scanDirectoryToConfig(Path::join($plugin->getDataFolder(), "Economy")) as $info) {
            if (!$info->exists("name")) {
                unset($info);
                continue;
            }
            $this->addEconomy(new Economy($info->get("name"), $info->get("symbol", "$"), $info->get("default", 0)));
            unset($info);
        }
    }

    public function addEconomy(Economy $economy): void
    {
        $this->listEconomy[$economy->getId()] = $economy;
        Main::getInstance()->getLogger()->info("Economy {$economy->getName()} added");
    }

    public function getEconomy(string $id): ?Economy
    {
        return $this->listEconomy[$id] ?? null;
    }


    /**
     * @return Economy[]
     */
    public function getEconomyList(): array
    {
        return $this->listEconomy;
    }

    public function isEconomy(string $id): bool
    {
        return isset($this->listEconomy[$id]);
    }


}