<?php

namespace SenseiTarzan\MultiEconomy\Commands\args;

use CortexPE\Commando\args\TargetPlayerArgument;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class PlayerArgument extends TargetPlayerArgument
{
    public function parse(string $argument, CommandSender $sender): string
    {
        return Server::getInstance()->getPlayerExact($argument) ?? strtolower($argument);
    }
}