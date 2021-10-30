<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ZenGroup;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

/**
 * @method ZenGroup|Builder query()
 * @method ZenGroup|null find(int $id)
 */
class ZenGroupsRepository extends Repository
{
    #[Pure]
     public function __construct(ZenGroup $model)
     {
         parent::__construct($model);
     }

    public function forActualize(): Builder|ZenGroup
    {
        return $this
            ->query()
            /* @uses \App\Models\ZenGroup::integrations() */
            ->with('integrations');
    }
}
