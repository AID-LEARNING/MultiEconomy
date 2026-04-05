# MultiEconomy - Documentation (Français)

## Sommaire
- [Vue d'ensemble](#vue-densemble)
- [Installation](#installation)
- [Configuration](#configuration)
- [Economies](#economies)
- [Commandes](#commandes)
- [Permissions](#permissions)
- [API developpeur](#api-developpeur)
- [Evenements](#evenements)
- [Notes techniques](#notes-techniques)
- [Depannage](#depannage)
- [FAQ](#faq)

## Vue d'ensemble
MultiEconomy est un plugin PocketMine-MP qui permet de gerer plusieurs monnaies sur un meme serveur.

Cette documentation couvre:
- l'installation
- la configuration
- les permissions
- l'utilisation des commandes
- l'integration developpeur (API + evenements)

## Installation
1. Placez le plugin dans le dossier `plugins/`.
2. Demarrez le serveur une premiere fois.
3. Verifiez que les fichiers suivants sont disponibles:
   - `resources/config.yml`
   - `resources/Economy/Money.yml`
4. Redemarrez le serveur apres toute modification de configuration.

## Configuration
Fichier: `resources/config.yml`

```yaml
---
data-type: json
legacy-mode-command: false
...
```

- `data-type`: backend de stockage (`json`, `yaml`, `yml`, `custom`)
- `legacy-mode-command`: active la compatibilite des anciennes syntaxes de commande

## Economies
Les economies sont chargees depuis `resources/Economy/` (exemple: `Money.yml`).

Champs courants:
- `name`: identifiant/nom de la monnaie
- `default`: solde initial
- `symbol`: symbole d'affichage

## Commandes
Commande principale:
- `src/SenseiTarzan/MultiEconomy/Commands/EconomyCommand.php`

Sous-commandes:
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/AddBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/SubtractBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/SetBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/PayBalanceSubCommand.php`
- `src/SenseiTarzan/MultiEconomy/Commands/subCommand/TopBalanceSubCommand.php`

Utilisez `/help` en jeu pour la syntaxe exacte de votre version.

## Permissions
Source: `plugin.yml`

| Permission | Defaut | Description |
|---|---|---|
| `multieconomy.command` | `true` | Acces a la commande principale |
| `multieconomy.command.see` | `op` | Voir le solde d'un joueur cible |
| `multieconomy.command.add` | `op` | Ajouter un montant |
| `multieconomy.command.subtract` | `op` | Retirer un montant |
| `multieconomy.command.set` | `op` | Definir un solde |
| `multieconomy.command.pay` | `true` | Payer un autre joueur |
| `multieconomy.command.top` | `true` | Voir le classement |

## API developpeur
Classes centrales:
- `src/SenseiTarzan/MultiEconomy/Component/MultiEconomyManager.php`
- `src/SenseiTarzan/MultiEconomy/Component/EcoPlayerManager.php`
- `src/SenseiTarzan/MultiEconomy/Class/Economy/Economy.php`
- `src/SenseiTarzan/MultiEconomy/Class/Player/EcoPlayer.php`

Recuperer une economie:
```php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;

$economy = MultiEconomyManager::getInstance()->getEconomy("money");
```

Lire un solde (asynchrone):
```php
use SOFe\AwaitGenerator\Await;
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;

Await::g2c(
    MultiEconomyManager::getInstance()->getEconomy("money")->get($player),
    function (float $balance): void {
        // utiliser $balance
    },
    [
        RuntimeException::class => function (): void {}
    ]
);
```

Exceptions metier courantes:
- `EconomyNoHasAmountException`
- `EconomyUpdateException`
- `InfiniteValueException`

## Evenements
Dossier: `src/SenseiTarzan/MultiEconomy/Events/`

- `EcolPlayerLoadedEvent`
- `EconomyChangeDataEvent`

Usages typiques:
- lancer une logique quand les donnees eco d'un joueur sont chargees
- reagir aux changements de solde

## Notes techniques
- Listener joueur: `src/SenseiTarzan/MultiEconomy/Listener/PlayerListener.php`
- Tache asynchrone: `src/SenseiTarzan/MultiEconomy/Task/AsyncSortTask.php`
- Soft dependency declaree: `Middleware`

## Depannage
- Verifiez les permissions si une commande est refusee.
- Verifiez le nom/ID exact de l'economie.
- Verifiez les logs serveur en cas d'exception.
- En migration, testez `legacy-mode-command: true`.

## FAQ
**Q: Puis-je utiliser plusieurs monnaies ?**  
Oui.

**Q: Le plugin supporte-t-il l'API 5 ?**  
Oui, `plugin.yml` declare `api: 5.0.0`.

**Q: Quelles permissions donner aux joueurs ?**  
En general: `multieconomy.command`, `multieconomy.command.pay`, `multieconomy.command.top`.
