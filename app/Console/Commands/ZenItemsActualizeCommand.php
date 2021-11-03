<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Managers\ZenResourceResultsManager;
use App\Models\ZenGroup;
use App\Models\ZenRawItem;
use App\Repositories\ZenGroupsRepository;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Helper;
use function date;
use function memory_get_usage;
use function microtime;

/**
 * Помечаем данные ZenMoney готовыми к отправке в MSC.
 *
 * Тест производительности на 1 группе в которой 7811 элементов
 * memory: 30.0 MiB
 * time: 5 minutes
 * avg: 26 items per second
 */
class ZenItemsActualizeCommand extends Command
{
    /** {@inheritdoc} */
    protected $signature = 'actualize:zen { --group=* : Groups for iterate over }';

    /** {@inheritdoc} */
    protected $description = 'Prepare zen items for upload to msc';

    private ZenGroupsRepository       $groupsRepository;
    private ZenResourceResultsManager $manager;

    public function __construct(
        ZenGroupsRepository $integrationsRepository,
        ZenResourceResultsManager $manager
    ) {
        parent::__construct();
        $this->groupsRepository = $integrationsRepository;
        $this->manager = $manager;
    }

    public function handle(): void
    {
        $groupsIds = (false !== $inputGroups = $this->option('group')) ? (array)$inputGroups : null;

        $query = $this->groupsRepository->forActualize();
        if (null !== $groupsIds) {
            $query->whereIn('id', $groupsIds);
        }

        $this->info('Query: '.$query->toSql());

        $groups = $query->createTableIterator(1000);
        $totalSteps = $groups->count();
        $this->line("Start iterate over groups {$totalSteps} from {$groups->getMinId()} to {$groups->getMaxId()}");

        /** @var ZenGroup $group */
        foreach ($groups as $group) {
            $datetime = date('Y-m-d H:i:s');
            $startedAt = microtime(true);

            $query = $group
                ->items()
                ->with('results')
                ->where('need_result', true);

            $this->info('Query: '.$query->toSql());

            $items = $query
                ->createTableIterator(1000)
            ;
            $totalSteps = $items->count();

            $this->line(
                "Start proceed group {$group->id} at {$datetime} from {$items->getMinId()} to {$items->getMaxId()}"
            );

            $bar = $this->getOutput()->createProgressBar($totalSteps);
            $bar->setFormat($bar::FORMAT_DEBUG);

            /** @var ZenRawItem $item */
            foreach ($bar->iterate($items, $totalSteps) as $item) {
                $item->setRelation('group', $group);
                $this->manager->actualize($item);
            }

            $group->markSynced()->save();

            /* @noinspection DisconnectedForeachInstructionInspection */
            $this->newLine();

            $this->table(
                ['memory', 'ttp', 'datetime'],
                [[
                     Helper::formatMemory(memory_get_usage(true)),
                     Helper::formatTime(microtime(true) - $startedAt),
                     date('Y-m-d H:i:s'),
                 ]]
            );
        }
    }
}
