<?php

namespace SenseiTarzan\MultiEconomy\Class\Player;

use JsonSerializable;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;
use Throwable;

class EcoPlayer implements JsonSerializable
{

    private string $id;

    public function __construct(private readonly string $name, private array $economy)
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
     * @param string $id
     * @return float
     * @internal
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

    public function firstConnection(): void
    {
        foreach (MultiEconomyManager::getInstance()->getEconomyList() as $id => $economy) {
            if ($this->existsEconomy($id)) {
                continue;
            }
            $this->economy[$id] = $economy->getDefault();
            Await::g2c($economy->set($this->getName(), $economy->getDefault()), function () {
            }, function (Throwable $e) {
                Main::getInstance()->getLogger()->error($e->getMessage());
            });
        }
    }


    public function jsonSerialize(): array
    {
        return $this->economy;
    }
}