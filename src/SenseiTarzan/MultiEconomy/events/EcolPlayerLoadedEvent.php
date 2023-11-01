<?php

namespace SenseiTarzan\MultiEconomy\events;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class EcolPlayerLoadedEvent extends PlayerEvent
{

    public function __construct(Player $player)
    {
        $this->player = $player;
    }
}