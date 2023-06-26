<?php

namespace SenseiTarzan\MultiEconomy\Class\Economy;

use Generator;
use pocketmine\player\Player;
use pocketmine\Server;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\MultiEconomy\Class\Exception\EconomyNoHasAmountException;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;
class Economy
{

    private readonly string $id;

    public function __construct(private readonly string $name, private readonly string $symbol, private readonly float $default)
    {
        $this->id = strtolower($name);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getDefault(): float
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }


    /**
     * @param Player|string $player
     * @param float $amount
     * @return Generator <bool> online or offline
     */
    public function add(Player|string $player, float $amount): Generator
    {
        $name= $player instanceof Player ? $player->getName() : $player;
        Main::getInstance()->getLogger()->info("Creation de la promesse de add de " . $name . " de " . $amount . " " . $this->getName());
        return Await::promise(function ($resolve, $reject) use ($player, $amount, $name): void {
            Await::f2c(function () use ($player, $amount, $name) {
                if (is_string($player)) {
                    $player = Server::getInstance()->getPlayerExact($player) ?? $player;
                }
                if ($player instanceof Player) {
                    EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "add", ["economy" => $this->getId(), "amount" => $amount]));
                    Main::getInstance()->getLogger()->info("Promesse de add de " . $name . " de " . $amount . " " . $this->getName() . " terminé");
                    return true;
                }
                yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "add", ["economy" => $this->getId(), "amount" => $amount]);
                Main::getInstance()->getLogger()->info("Promesse de add de " . $name . " de " . $amount . " " . $this->getName() . " terminé");
                return false;

            }, $resolve, $reject);
        });
    }

    /**
     * @param Player|string $player
     * @param float $amount
     * @return Generator <bool> online or offline receiver
     */
    public function subtract(Player|string $player, float $amount): Generator
    {
        $name= $player instanceof Player ? $player->getName() : $player;
        Main::getInstance()->getLogger()->info("Creation de la promesse de substract de " . $name . " pour " . $amount . " " . $this->getName());
        return Await::promise(function ($resolve, $reject) use ($player, $amount,$name) {
            Await::f2c(function () use ($player, $amount,$name) {
                if (is_string($player)) {
                    $player = Server::getInstance()->getPlayerExact($player) ?? $player;
                }
                if ($player instanceof Player) {
                    EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "subtract", ["economy" => $this->getId(), "amount" => $amount]));
                    Main::getInstance()->getLogger()->info("Promesse de substract de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
                    return true;
                }
                yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "subtract", ["economy" => $this->getId(), "amount" => $amount]);
                Main::getInstance()->getLogger()->info("Promesse de substract de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
                return false;
            }, $resolve, $reject);
        });
    }

    /**
     * @param Player|string $player
     * @param float $amount
     * @return Generator <bool> online or offline receiver
     */
    public function set(Player|string $player, float $amount): Generator
    {
        $name= $player instanceof Player ? $player->getName() : $player;
        Main::getInstance()->getLogger()->info("Creation de la promesse de set de " . $name . " pour " . $amount . " " . $this->getName());
        return Await::promise(function ($resolve, $reject) use ($player, $amount, $name) {
            Await::f2c(function () use ($player, $amount, $name) {

                if (is_string($player)) {
                    $player = Server::getInstance()->getPlayerExact($player) ?? $player;
                }
                if ($player instanceof Player) {
                    EcoPlayerManager::getInstance()->getEcoPlayer($player)?->setEconomy($this->getId(), yield from DataManager::getInstance()->getDataSystem()->updateOnline($player->getName(), "set", ["economy" => $this->getId(), "amount" => $amount]));
                    Main::getInstance()->getLogger()->info("Promesse de set de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
                    return true;
                }
                yield from DataManager::getInstance()->getDataSystem()->updateOffline($player, "set", ["economy" => $this->getId(), "amount" => $amount]);
                Main::getInstance()->getLogger()->info("Promesse de set de " . $name . " pour " . $amount . " " . $this->getName() . " terminé");
                return false;
            }, $resolve, $reject);
        });
    }

    /**
     * @param Player|string $player
     * @return Generator <float>
     */
    public function get(Player|string $player): Generator
    {
        return DataManager::getInstance()->getDataSystem()->createPromiseGetBalance($player, $this->getId());
    }

    /**
     * @param Player $sender
     * @param Player|string $receiver
     * @param float $amount
     * @return Generator <bool> online or offline receiver
     */
    public function pay(Player|string $sender, Player|string $receiver, float $amount): Generator
    {
        Main::getInstance()->getLogger()->info("Creation de la promesse de pay de " . ($sender instanceof Player ? $sender->getName() : $sender) . " vers " . ($receiver instanceof Player ? $receiver->getName() : $receiver) . " pour " . $amount . " " . $this->getName());
        return Await::promise(function ($resolve, $reject) use ($sender, $receiver, $amount) {
            Await::f2c(function () use ($sender, $receiver, $amount): Generator {
                yield from $this->has($sender, $amount);
                yield from $this->subtract($sender, $amount);
                return yield from $this->add($receiver, $amount);
            }, function (bool $result) use ($resolve, $reject, $sender, $receiver, $amount) {
                Main::getInstance()->getLogger()->info("Promesse de pay de " . ($sender instanceof Player ? $sender->getName() : $sender) . " vers " . ($receiver instanceof Player ? $receiver->getName() : $receiver) . " pour " . $amount . " " . $this->getName() . " terminé");
                $resolve($result);
            }, $reject);
        });
    }

    public function has(Player|string $player, float $amount): Generator
    {
        return Await::promise(function ($resolve, $reject) use ($player, $amount) {
            Await::g2c($this->get($player), function ($result) use ($resolve, $reject, $amount) {
                if ($result >= $amount){
                    $resolve();
                    return;
                }
                $reject(new EconomyNoHasAmountException());
            });
        });
    }
}