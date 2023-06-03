<?php

namespace SenseiTarzan\MultiEconomy\Task;

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;

/**
 * @internal MultiEconomy
 */
final class AsyncSortTask extends AsyncTask
{


    private ThreadSafeArray $ecomony;

    public function __construct(private readonly string $economy, private readonly int $limit, array $data, $resolve)
    {
        $this->ecomony = ThreadSafeArray::fromArray($data);
        $this->storeLocal("resolve", $resolve);
    }

    public function onRun(): void
    {
        $all = (array)$this->ecomony;
        array_walk($all, function (&$value) {
            $value = $value[$this->economy];
        });
        arsort($all);
        $this->setResult(ThreadSafeArray::fromArray(array_slice($all, 0, $this->limit)));
    }

    public function onCompletion(): void
    {
        $resolve = $this->fetchLocal("resolve");
        $resolve((array)$this->getResult());
    }

}