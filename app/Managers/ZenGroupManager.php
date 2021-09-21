<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\ZenGroup;
use App\Models\ZenUser;
use Illuminate\Support\Facades\Log;

/**
 * Класс замыкает на себе управление группами пользователей ZenMoney.
 */
class ZenGroupManager
{
    /**
     * Находит в БД или создаёт новую группу пользователей ZenMoney.
     *
     * @param string $adminId идентификатор администратора группы
     *
     * @return ZenGroup
     */
    public function findOrCreateByAdmin(string $adminId): ZenGroup
    {
        $groupId = ZenUser::where(['zen_user_id' => $adminId])->pluck('group_id')->first();
        $group = $groupId ? ZenGroup::findOrNew($groupId) : new ZenGroup();

        Log::debug('Group', $group->attributesToArray());

        if (!$group->exists) {
            $group->save();
            // Сохраняем только админа, т.к. остальные пользователи нас не интересуют
            ZenUser::firstOrCreate(['zen_user_id' => $adminId], ['group_id' => $group->id]);
        }

        return $group;
    }
}
