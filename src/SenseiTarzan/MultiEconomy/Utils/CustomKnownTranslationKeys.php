<?php

/*
 *
 *            _____ _____         _      ______          _____  _   _ _____ _   _  _____
 *      /\   |_   _|  __ \       | |    |  ____|   /\   |  __ \| \ | |_   _| \ | |/ ____|
 *     /  \    | | | |  | |______| |    | |__     /  \  | |__) |  \| | | | |  \| | |  __
 *    / /\ \   | | | |  | |______| |    |  __|   / /\ \ |  _  /| . ` | | | | . ` | | |_ |
 *   / ____ \ _| |_| |__| |      | |____| |____ / ____ \| | \ \| |\  |_| |_| |\  | |__| |
 *  /_/    \_\_____|_____/       |______|______/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author AID-LEARNING
 * @link https://github.com/AID-LEARNING
 *
 */

declare(strict_types=1);

namespace SenseiTarzan\MultiEconomy\Utils;

class CustomKnownTranslationKeys
{
	const ADD_ECONOMY_SENDER = "Economy.sender.add";
	const ADD_ECONOMY_RECEIVER = "Economy.receiver.add";

	const SUBTRACT_ECONOMY_SENDER = "Economy.sender.subtract";
	const SUBTRACT_ECONOMY_RECEIVER = "Economy.receiver.subtract";

	const SET_ECONOMY_SENDER = "Economy.sender.set";
	const SET_ECONOMY_RECEIVER = "Economy.receiver.set";

	const PAY_ECONOMY_SENDER = "Economy.sender.pay";
	const PAY_ECONOMY_RECEIVER = "Economy.receiver.pay";

	const BALANCE_ECONOMY_SENDER = "Economy.sender.balance";

	const HEADER_ECONOMY_TOP = "Economy.top.header";
	const BODY_ECONOMY_TOP = "Economy.top.body";

	const ERROR_TARGET_NOT_FOUND = "Error.target.notFound";
	const ERROR_TARGET_NOT_ONLINE = "Error.target.notOnline";

	const ERROR_TARGET_YOURSELF = "Error.target.yourSelf";

	const ERROR_NOT_ENOUGH_MONEY = "Error.notEnoughAmount";

	const ERROR_NEGATIVE_AMOUNT = "Error.negativeAmount";
}
