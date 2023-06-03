<?php

namespace SenseiTarzan\MultiEconomy\Class\Save;

use Exception;
use Generator;
use pmmp\thread\ThreadSafeArray;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Task\AsyncSortTask;
use SOFe\AwaitGenerator\Await;
use Throwable;

final class YAMLSave extends IDataSaveEconomy
{
    private Config $data;

    public function __construct(Main $plugin)
    {
        $this->data = new Config($plugin->getDataFolder() . "data.yml", Config::YAML);
        //$this->data->setAll(array_merge($this->data->getAll(), Main::getInstance()->generateFakePlayers(10000)));
    }

    public function getName(): string
    {
        return "YAML";
    }

    /**
     * @param Player|string $player
     * @return Generator
     */
    public function createPromiseEconomy(Player|string $player): Generator
    {
        return Await::promise(function ($resolve) use ($player) {
            $resolve($this->data->get(strtolower($player instanceof Player ? $player->getName() : $player), []));
        });
    }

    /**
     * @param string $id
     * @param string $type
     * @param mixed $data
     * @return Generator
     */
    public function createPromiseUpdate(string $id, string $type, mixed $data): Generator
    {
        $id = strtolower($id);
        return Await::promise(function ($resolve, $reject) use ($id, $type, $data) {
            try {
                $economyType = strtolower($data["economy"]);
                $balance = $data["amount"];
                switch (strtolower($type)) {
                    case "add":
                    {
                        $balance = $this->data->getNested($id . ".$economyType");
                        $balance += $data["amount"];
                        break;
                    }
                    case "subtract":
                    {
                        $balance = $this->data->getNested($id . ".$economyType");
                        $balance -= $data["amount"];
                        if ($balance < 0) {
                            $balance = 0;
                        }
                        break;
                    }
                }
                $this->data->setNested($id . ".$economyType", $balance);
                $this->data->save();
                $resolve($balance);
            } catch (Throwable) {
                $reject(new Exception(code: 403));
            }
        });
    }
    public function createPromiseTop(string $economy, int $limite = 10): Generator
    {
        return Await::promise(function ($resolve) use ($economy, $limite) {
            Server::getInstance()->getAsyncPool()->submitTask(new AsyncSortTask($economy, $limite - 1, $this->data->getAll(), $resolve));
        });
    }
}