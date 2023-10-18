<?php

namespace SenseiTarzan\MultiEconomy\Component;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;

final class EcoPlayerManager
{

    use SingletonTrait;

    /**
     * @var EcoPlayer[] $listEcoPlayer
     * @phpstan-var array<string, EcoPlayer>
     */
    private array $listEcoPlayer = [];

    public function __construct()
    {
    }

    /**
     * @param EcoPlayer $ecoPlayer
     * @return void
     */
    public function addEcoPlayer(EcoPlayer $ecoPlayer): void
    {
        $this->listEcoPlayer[$ecoPlayer->getId()] = $ecoPlayer;
        $ecoPlayer->firstConnection();

    }

    public function removeEcoPlayer(Player $player): void
    {
        unset($this->listEcoPlayer[strtolower($player->getName())]);
    }

    public function getEcoPlayer(Player|string $player): ?EcoPlayer
    {
        return $this->listEcoPlayer[strtolower($player instanceof Player ? $player->getName() : $player)] ?? null;
    }

}