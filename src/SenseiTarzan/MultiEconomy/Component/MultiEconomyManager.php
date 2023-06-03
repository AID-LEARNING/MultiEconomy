<?php

namespace SenseiTarzan\MultiEconomy\Component;

use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Economy\Economy;
use SenseiTarzan\MultiEconomy\Main;

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
        foreach ($plugin->getConfig()->getAll() as $name => $info) {
            $this->addEconomy(new Economy($name, $info["symbol"] ?? "$", $info["default"] ?? 0));
        }
    }

    public function addEconomy( Economy $economy): void
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