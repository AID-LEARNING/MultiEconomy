<?php

namespace SenseiTarzan\MultiEconomy\Class\Player;

use JsonSerializable;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;

class EcoPlayer implements JsonSerializable
{

    private string $id;

    public function __construct(private string $name, private array $economy)
    {
        $this->id = strtolower($this->name);
    }
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @internal
     * @param string $id
     * @return float
     */
    public function getEconomy(string $id): float
    {
        return $this->economy[$id] ?? 0.0;
    }

    public function setEconomy(string $id, float $amount): void
    {
        $this->economy[$id] = $amount;
    }

    public function existsEconomy(string $id): bool
    {
        return isset($this->economy[$id]);
    }

    public function firstConnection(): void{
        foreach (MultiEconomyManager::getInstance()->getEconomyList() as $id => $economy) {
            if ($this->existsEconomy($id)) {
                continue;
            }
            $this->economy[$id] = $economy->getDefault();
            DataManager::getInstance()->getDataSystem()->updateOnline($this->getId(), "set", ["economy" => $id, "amount" => $economy->getDefault()]);
        }
    }


    public function jsonSerialize(): array
    {
        return $this->economy;
    }
}