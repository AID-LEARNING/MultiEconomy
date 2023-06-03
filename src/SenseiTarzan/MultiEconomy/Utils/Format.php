<?php

namespace SenseiTarzan\MultiEconomy\Utils;

use pocketmine\utils\TextFormat;

class Format
{

    public static function nameToId(string $name): string
    {
        return str_replace(array_values(TextFormat::COLORS), "", strtolower(str_replace([" "], ["_"], $name)));
    }

}