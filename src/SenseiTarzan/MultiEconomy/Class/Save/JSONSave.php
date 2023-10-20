<?php

namespace SenseiTarzan\MultiEconomy\Class\Save;

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyUpdateException;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Main;
use SenseiTarzan\MultiEconomy\Task\AsyncSortTask;
use SOFe\AwaitGenerator\Await;
use Throwable;

final class JSONSave extends IDataSaveEconomy
{
    private Config $data;

    public function __construct(Main $plugin)
    {
        $this->data = new Config($plugin->getDataFolder() . "data.json", Config::JSON);
    }

    public function getName(): string
    {
        return "JSON";
    }

    /**
     * @param Player|string $player
     * @return Generator
     */
    public function createPromiseEconomy(Player|string $player): Generator
    {
        return Await::promise(function ($resolve) use ($player) {
            $resolve($this->data->get(($player instanceof Player ? $player->getName() : $player), []));
        });
    }

    public function createPromiseGetBalance(Player|string $player, string $economy): Generator
    {
        return Await::promise(function ($resolve) use ($player, $economy) {
            $resolve(EcoPlayerManager::getInstance()->getEcoPlayer($player)?->getEconomy($economy) ?? $this->data->getNested(($player instanceof Player ? $player->getName() : $player) . ".$economy", 0));
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
                    case "multiply":
                    {
                        $balance = $this->data->getNested($id . ".$economyType");
                        $balance *= $data["amount"];
                        break;
                    }
                    case "division":
                    {
                        $balance = $this->data->getNested($id . ".$economyType");
                        if ($data["amount"] === 0)
                        {
                            $reject(new \InvalidArgumentException("Dont you cant divided with zero"));
                            return ;
                        }
                        $balance /= $data["amount"];
                        break;
                    }
                }
                $this->data->setNested($id . ".$economyType", $balance);
                $this->data->save();
                $resolve($balance);
            } catch (Throwable) {
                $reject(new EconomyUpdateException("Error updating economy $economyType for $id with $type " . $data["amount"]));
            }
        });
    }

    public function createPromiseTop(string $economy, int $limite = 10): Generator
    {
        return Await::promise(function ($resolve) use ($economy, $limite) {
            Server::getInstance()->getAsyncPool()->submitTask(new AsyncSortTask($economy, $limite, $this->data->getAll(), $resolve));
        });
    }
}