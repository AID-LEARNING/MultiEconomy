<?php

namespace SenseiTarzan\MultiEconomy\Listener;

use Generator;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use SenseiTarzan\DataBase\Component\DataManager;
use SenseiTarzan\ExtraEvent\Class\EventAttribute;
use SenseiTarzan\MultiEconomy\Component\EcoPlayerManager;
use SenseiTarzan\MultiEconomy\Main;
use SOFe\AwaitGenerator\Await;

final class PlayerListener
{
    #[EventAttribute(EventPriority::LOW)]
    public function onJoin(PlayerJoinEvent $event): void
    {
        DataManager::getInstance()->getDataSystem()->loadDataPlayer($event->getPlayer());
    }

    #[EventAttribute(EventPriority::LOW)]
    public function onQuit(PlayerQuitEvent $event): void
    {
        EcoPlayerManager::getInstance()->removeEcoPlayer($event->getPlayer());

    }

}