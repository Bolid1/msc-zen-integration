<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Managers\ZenResourceResultsManager;
use App\Repositories\ZenRawItemsRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ActualizeZenResourceResultJob extends Job implements ShouldBeUnique
{
    private int $zenRawItemId;

    /**
     * @param int $zenRawItemId
     */
    public function __construct(int $zenRawItemId)
    {
        $this->zenRawItemId = $zenRawItemId;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return (string)$this->zenRawItemId;
    }

    public function handle(
        ZenRawItemsRepository $repository,
        ZenResourceResultsManager $manager
    ): void {
        if ($item = $repository->find($this->zenRawItemId)) {
            $manager->actualize($item);
        }
    }
}
