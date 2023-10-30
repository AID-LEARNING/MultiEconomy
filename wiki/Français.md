
## Français
Configuration de la ``plugin_data/RoleManager/config.yml``

| Clé       | Description                                                   | valuer attendu                                                                                       |
|-----------|---------------------------------------------------------------|------------------------------------------------------------------------------------------------------|
| data-type | Permet de définir le system de donnée de sauvegarde du joueur | ``json`` is default <br/>  ``yaml``  <br/> ``yml``<br/> ``custom``  pour les personnes experimenters |

# Creation d'une économie

| Clé     | Description                                | type attendu   | obligatoire          |
|---------|--------------------------------------------|----------------|----------------------|
| name    | Le nom de la monnaie a créer               | texte          | **oui**              |
| default | l'argent donner a la creation de ça bourse | nombre decimal | **non** 0 par défaut |
| symbol  | le symbole de la monnaie.                  | texte          | **non** $ par défaut |
Votre fichier doit être dans ``plugin_data/MultiEconomy/Economy`` car sinon les économies ne seront pas initialiser
et vous devais faire vos économies en .yml

```yaml
---
name: Livre Starling
default: 0
symbol: £
...
```

# Creation de votre propre system de sauvegarde de données du joueur
### [⚠️⚠️] Ceci est un exemple, je ne cherche pas l'optimisation, mais montre comment utiliser et vous devez entre experimenter pour le faire

[https://github.com/AID-LEARNING/MultiEconomySQL](Exemple en sql)

# Récupérer l'Economy par rapport à l'id

````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
MultiEconomyManager::getInstance()->getEconomy("nom de l'économie");
````

# Récupérer la bourse de le jouer par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;
Await::g2c(MultiEconomyManager::getInstance()->getEconomy("nom de l'économie")->get(Player or string), function (float $balance) {
    
}, [
    \poggit\libasynql\SqlError::class => function(){},
    RuntimeException::class => function() {}
]);
````

# Definir la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->set(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````

# Ajouter de l'argent dans la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->add(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````

# Enlever de l'argent dans la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->subtract(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````
# Multiplie la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->multiply(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````
# Diviser la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy($id)->division(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception)  {
        
    },
]);
````
# Mettre un pourcentage de la bourse d'un joueur par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;

Await::g2c(MultiEconomyManager::getInstance()->getEconomy("nom de l'économie")->percent(player: Player or string, amount: float),
function (bool $online/*detect si le joueur est en ligne*/) {
    
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````
# Recuperer le top par économie
````php
use SenseiTarzan\MultiEconomy\Component\MultiEconomyManager;
use SOFe\AwaitGenerator\Await;
use SenseiTarzan\MultiEconomy\Utils\Format;
Await::g2c(DataManager::getInstance()->getDataSystem()->createPromiseTop(economy: "nom de l'économie", limit: int),
function (ThreadSafeArray $result) {
    $arrayTop = Format::threadSafeArrayToArray($result);
}, [
    EconomyUpdateException::class => function (EconomyUpdateException $exception) {
        
    },
]);
````
