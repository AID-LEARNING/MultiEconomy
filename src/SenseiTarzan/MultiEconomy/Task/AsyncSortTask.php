<?php

namespace SenseiTarzan\MultiEconomy\Task;

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;
use SenseiTarzan\MultiEconomy\Utils\Format;

/**
 * @internal MultiEconomy
 */
final class AsyncSortTask extends AsyncTask
{


    private ThreadSafeArray $data;

    public function __construct(private readonly string $economy, private readonly int $limit, array $data, $resolve)
    {
        $this->data = Format::arrayToThreadSafeArray($data);
        $this->storeLocal("resolve", $resolve);
    }

    public function onRun(): void
    {
        $all = (array)$this->data;
        array_walk($all, function (&$value) {
            $value = $value[$this->economy];
        });
        arsort($all);
        $this->setResult(Format::arrayToThreadSafeArray(array_slice($all, 0, $this->limit)));
    }

    public function onCompletion(): void
    {
        $resolve = $this->fetchLocal("resolve");
        $resolve($this->getResult());
    }

}