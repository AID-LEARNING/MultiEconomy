<?php

namespace SenseiTarzan\MultiEconomy\Class\Save;

use Error;
use Exception;
use Generator;
use PHPUnit\Event\Code\Throwable;
use pocketmine\player\Player;
use SenseiTarzan\DataBase\Class\IDataSave;
use SenseiTarzan\MultiEconomy\Class\Player\EcoPlayer;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SOFe\AwaitGenerator\Await;
use TypeError;

abstract class IDataSaveEconomy implements IDataSave
{


    const SUBTRACT = "subtract";
    const SET = "set";
    const ADD = "add";
    private const PAY = "pay";


    final public function loadDataPlayer(Player|string $player): void
    {
        Await::f2c(function () use ($player): Generator{
            $data = yield from $this->createPromiseEconomy($player);
            return (yield from EcoPlayerManager::getInstance()->addEcoPlayer(new EcoPlayer($player, $data)));
        }, null, function (Exception|Error $throwable) use ($player){
            if ($player instanceof Player){
                $player->kick($throwable->getMessage());
            }
        });
    }

    abstract protected function createPromiseAllBalance(Player|string $player): Generator;

    abstract public function createPromiseEconomy(Player|string $player): Generator;

    /**
     * @param Player|string $player
     * @param string $economy
     * @return Generator<float>
     */
    abstract public function createPromiseGetBalance(Player|string $player, string $economy): Generator;

    /**
     * @param string $id
     * @param string $type
     * @param mixed $data
     * @return Generator
     */
    final public function updateOnline(string $id, string $type, mixed $data): Generator
    {
        return $this->createPromiseUpdate($id, $type, $data);
    }

    /**
     * @inheritDoc
     */
    final public function updateOffline(string $id, string $type, mixed $data): Generator
    {
        return $this->createPromiseUpdate($id, $type, $data);
    }


    abstract public function createPromiseUpdate(string $id, string $type, mixed $data): Generator;


    abstract function createPromiseTop(string $economy, int $limite): Generator;

}