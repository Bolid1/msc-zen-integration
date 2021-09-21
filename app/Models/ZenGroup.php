<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Объединение пользователей ZenMoney.
 *
 * @property positive-int $id Идентификатор группы
 *
 * @mixin Builder
 *
 * @method static ZenGroup firstOrCreate(array $attributes = [], array $values = [])
 * @method static ZenGroup findOrNew($id, $columns = ['*'])
 * @method static Builder|ZenGroup newModelQuery()
 * @method static Builder|ZenGroup newQuery()
 * @method static Builder|ZenGroup query()
 */
class ZenGroup extends Model
{
}