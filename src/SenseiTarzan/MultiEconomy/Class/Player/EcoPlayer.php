<?php

namespace SenseiTarzan\MultiEconomy\Class\Player;

use JsonSerializable;
use pocketmine\player\Player;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;
use Throwable;

class EcoPlayer implements JsonSerializable
{

    private string $id;

    public function __construct(private readonly Player $player, private array $economy)
    {
        $this->id = strtolower($this->player->getName());
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
        return $this->player->getName();
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


    public function jsonSerialize(): array
    {
        return $this->economy;
    }
}