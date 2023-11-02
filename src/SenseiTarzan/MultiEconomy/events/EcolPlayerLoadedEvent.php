<?php

namespace SenseiTarzan\MultiEconomy\events;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;

class EcolPlayerLoadedEvent extends PlayerEvent
{

    public function __construct(Player $player, private readonly EcoPlayer $ecoPlayer)
    {
        $this->player = $player;
    }

    /**
     * @return EcoPlayer
     */
    public function getEcoPlayer(): EcoPlayer
    {
        return $this->ecoPlayer;
    }
}