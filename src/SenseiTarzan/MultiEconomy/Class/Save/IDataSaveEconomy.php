<?php

namespace SenseiTarzan\MultiEconomy\Class\Save;

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use SenseiTarzan\DataBase\Class\IDataSave;
use SenseiTarzan\LanguageSystem\Component\LanguageManager;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Utils\CustomKnownTranslationFactory;
use SOFe\AwaitGenerator\Await;
use Throwable;

abstract class IDataSaveEconomy implements IDataSave
{


    const SUBTRACT = "subtract";
    const SET = "set";
    const ADD = "add";
    private const PAY = "pay";


    public function loadDataPlayer(Player|string $player): void
    {
        Await::g2c($this->createPromiseEconomy($player), function ($data) use ($player) {
            EcoPlayerManager::getInstance()->addEcoPlayer(new EcoPlayer($player instanceof Player ? $player->getName() : $player, $data));
        });
    }

    abstract public function createPromiseEconomy(Player|string $player): Generator;

    /**
     * @param string $id
     * @param string $type
     * @param mixed $data
     * @return Generator
     */
    public function updateOnline(string $id, string $type, mixed $data): Generator
    {
        return $this->createPromiseUpdate($id, $type, $data);
    }

    /**
     * @inheritDoc
     */
    public function updateOffline(string $id, string $type, mixed $data): Generator
    {
        return $this->createPromiseUpdate($id, $type, $data);
    }


    abstract public function createPromiseUpdate(string $id, string $type, mixed $data): Generator;


    abstract function createPromiseTop(string $economy, int $limite): Generator;

}