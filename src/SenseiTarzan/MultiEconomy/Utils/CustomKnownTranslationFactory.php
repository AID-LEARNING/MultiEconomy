<?php

namespace SenseiTarzan\MultiEconomy\Utils;

use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use SenseiTarzan\Kits\Class\Kits\Kit;

class CustomKnownTranslationFactory
{
    public static function add_economy_sender(Player|string $player, string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::ADD_ECONOMY_SENDER, ['player' => self::player_name($player), 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function add_economy_receiver(string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::ADD_ECONOMY_RECEIVER, [ 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function subtract_economy_sender(Player|string $player, string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::SUBTRACT_ECONOMY_SENDER, ['player' =>  self::player_name($player), 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function subtract_economy_receiver(string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::SUBTRACT_ECONOMY_RECEIVER, [ 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function set_economy_sender(Player|string $player, string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::SET_ECONOMY_SENDER, ['player' => self::player_name($player), 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function set_economy_receiver(string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::SET_ECONOMY_RECEIVER, [ 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function pay_economy_sender(Player|string $player, string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::PAY_ECONOMY_SENDER, ['player' => self::player_name($player), 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function pay_economy_receiver(Player|string $player, string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::PAY_ECONOMY_RECEIVER, ['player' => self::player_name($player), 'symbole' => $symbole, 'amount' => $amount]);
    }

    public static function balance_economy_sender(string $symbole, float $amount): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::BALANCE_ECONOMY_SENDER, ['symbole' => $symbole, 'amount' => $amount]);
    }

    public static function header_economy_top(int $limit, string $economy): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::HEADER_ECONOMY_TOP, ['limit' => $limit, 'economy' => $economy]);
    }

    public static function body_economy_top(int $rank, string $player, float $amount, string $symbole): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::BODY_ECONOMY_TOP, ['rank' => $rank, 'player' => $player, 'symbole' => $symbole, 'amount' => $amount]);
    }
    public static function error_target_not_online(string $target): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::ERROR_TARGET_NOT_ONLINE, ['target' => $target]);
    }

    public static function error_target_yourself(): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::ERROR_TARGET_YOURSELF);
    }

    public static function error_negative_amount(): Translatable
    {
        return new Translatable(CustomKnownTranslationKeys::ERROR_NEGATIVE_AMOUNT);
    }

    public static function error_not_enough_money(): Translatable{
        return new Translatable(CustomKnownTranslationKeys::ERROR_NOT_ENOUGH_MONEY);
    }


    private static function player_name(Player|string $player): string
    {
        if ($player instanceof Player) {
            return $player->getName();
        }
        return $player;
    }
}