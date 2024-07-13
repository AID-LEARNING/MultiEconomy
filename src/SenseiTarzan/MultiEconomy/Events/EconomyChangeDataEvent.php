<?php

namespace SenseiTarzan\MultiEconomy\Events;
use pocketmine\event\player\PlayerEvent;
use pocketmine\math\VectorMath;use pocketmine\player\Player;

class EconomyChangeDataEvent extends PlayerEvent
{

    public function __construct(Player $player, private readonly string $economy, private readonly float $amount)
    {
        $this->player = $player;
    }

    /**
     * @return string
     */
    public function getEconomy(): string
    {
        return $this->economy;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}