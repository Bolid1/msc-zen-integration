<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Managers\ZenRawItemsManager;

class ZenSaveRawItemJob extends Job
{
    private int    $groupId;
    private string $type;
    private array  $item;

    public function __construct(int $groupId, string $type, array $item)
    {
        $this->groupId = $groupId;
        $this->type = $type;
        $this->item = $item;
    }

    public function handle(ZenRawItemsManager $service): void
    {
        $service->save($this->groupId, $this->type, $this->item);
    }
}
