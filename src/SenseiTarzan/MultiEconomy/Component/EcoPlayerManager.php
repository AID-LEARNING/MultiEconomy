<?php

namespace SenseiTarzan\MultiEconomy\Component;

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SOFe\AwaitGenerator\Await;

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
     * @return Generator
     */
    public function addEcoPlayer(EcoPlayer $ecoPlayer): Generator
    {
        return Await::promise(function ($resolve) use($ecoPlayer): void{
            $this->listEcoPlayer[$ecoPlayer->getId()]  = $ecoPlayer;
            $resolve();
        });
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