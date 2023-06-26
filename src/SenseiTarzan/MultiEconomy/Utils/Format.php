<?php

namespace SenseiTarzan\MultiEconomy\Utils;

use pmmp\thread\ThreadSafeArray;
use pocketmine\utils\TextFormat;

class Format
{

    public static function nameToId(string $name): string
    {
        return str_replace(array_values(TextFormat::COLORS), "", strtolower(str_replace([" "], ["_"], $name)));
    }

    public static function threadSafeArrayToArray(ThreadSafeArray $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $value;
        }
        return $result;
    }

    public static function arrayToThreadSafeArray(array $array): ThreadSafeArray
    {
        $result = new ThreadSafeArray();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::arrayToThreadSafeArray($value);
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }

}