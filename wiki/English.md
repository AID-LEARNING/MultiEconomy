# MultiEconomy - Documentation (English)

## Table of contents
- [Overview](#overview)
- [Installation](#installation)
- [Configuration](#configuration)
- [Economies](#economies)
- [Commands](#commands)
- [Permissions](#permissions)
- [Developer API](#developer-api)
- [Events](#events)
- [Technical notes](#technical-notes)
- [Troubleshooting](#troubleshooting)
- [FAQ](#faq)

## Overview
MultiEconomy is a PocketMine-MP plugin that lets you manage multiple currencies on the same server.

## Installation
1. Put the plugin in your `plugins/` folder.
2. Start the server once.
3. Check that the following files are available:
   - `resources/config.yml`
   - `resources/Economy/Money.yml`
4. Restart the server after editing configuration files.

## Configuration
File: `resources/config.yml`

```yaml
---
data-type: json
legacy-mode-command: false
...
```

- `data-type`: data backend (`json`, `yaml`, `yml`, `custom`)
- `legacy-mode-command`: enables old command compatibility mode

## Economies
Economies are loaded from `resources/Economy/` (example: `Money.yml`).

Common fields:
- `name`: currency id/name
- `default`: starting balance
- `symbol`: display symbol

## Commands
Main command:
- `src/SenseiTarzan/MultiEconomy/Commands/EconomyCommand.php`

Subcommands:
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/AddBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/SubtractBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/SetBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/PayBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/TopBalanceSubCommand.php`

Use `/help` in-game for exact syntax on your running version.

## Permissions
Source: `plugin.yml`

| Permission | Default | Description |
|---|---|---|
| `multieconomy.command` | `true` | Access main command |
| `multieconomy.command.see` | `op` | See target balance |
| `multieconomy.command.add` | `op` | Add amount |
| `multieconomy.command.subtract` | `op` | Subtract amount |
| `multieconomy.command.set` | `op` | Set balance |
| `multieconomy.command.pay` | `true` | Pay other players |
| `multieconomy.command.top` | `true` | View top ranking |

## Developer API
Core classes:
- `src/SenseiTarzan/MultiEconomy/Component/MultiEconomyManager.php`
- `src/SenseiTarzan/MultiEconomy/Component/EcoPlayerManager.php`
- `src/SenseiTarzan/MultiEconomy/Class/Economy/Economy.php`
- `src/SenseiTarzan/MultiEconomy/Class/Player/EcoPlayer.php`

Get an economy:
```php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;

$economy = MultiEconomyManager::getInstance()->getEconomy("money");
```

Async balance read:
```php
use SOFe\AwaitGenerator\Await;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;

Await::g2c(
    MultiEconomyManager::getInstance()->getEconomy("money")->get($player),
    function (float $balance): void {
        // use $balance
    },
    [
        RuntimeException::class => function (): void {}
    ]
);
```

Common exceptions:
- `EconomyNoHasAmountException`
- `EconomyUpdateException`
- `InfiniteValueException`

## Events
Events folder: `src/SenseiTarzan/MultiEconomy/Events/`

- `EcolPlayerLoadedEvent`
- `EconomyChangeDataEvent`

## Technical notes
- Player listener: `src/SenseiTarzan/MultiEconomy/Listener/PlayerListener.php`
- Async task: `src/SenseiTarzan/MultiEconomy/Task/AsyncSortTask.php`
- Soft dependency: `Middleware`

## Troubleshooting
- Verify permission nodes when a command fails.
- Verify the exact economy name/id.
- Check server logs for update exceptions.
- During migration, test with `legacy-mode-command: true`.

## FAQ
**Q: Can I use multiple currencies?**  
Yes.

**Q: Is API 5 supported?**  
Yes, `plugin.yml` declares `api: 5.0.0`.

**Q: Which permissions should regular players have?**  
Usually `multieconomy.command`, `multieconomy.command.pay`, and `multieconomy.command.top`.
