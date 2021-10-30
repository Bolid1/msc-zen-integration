<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ZenRawItem;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

/**
 * Логика получения ({@see ZenRawItem}) из БД.
 *
 * @method ZenRawItem|Builder query()
 * @method ZenRawItem|null find(int $id)
 */
class ZenRawItemsRepository extends Repository
{
    #[Pure]
    public function __construct(ZenRawItem $model)
    {
        parent::__construct($model);
    }
}
