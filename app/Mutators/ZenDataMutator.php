<?php

declare(strict_types=1);

namespace App\Mutators;

use App\Models\ZenRawItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use function is_numeric;

class ZenDataMutator
{
    /**
     * Конвертируем данные из API в корректную модель ZenRawItem.
     *
     * @param string $type
     * @param array $item
     *
     * @return ZenRawItem|MessageBag полученная модель, либо список ошибок ({@see MessageBag}),
     *                               если данные извлечь не удалось
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function rawModelFromApi(string $type, array $item): ZenRawItem|MessageBag
    {
        $data = [
            'zen_id' => (string)('budget' === $type ? $item['tag'] : $item['id']),
            'data' => $item,
        ];

        $addon = 'deletion' === $type && isset($item['object'])
            ? [
                'type' => $item['object'],
                'changed_at' => isset($item['stamp']) && is_numeric($item['stamp'])
                    ? Carbon::createFromTimestamp($item['stamp'])
                    : null,
                'action' => ZenRawItem::ACTION_DEL,
            ]
            : [
                'type' => $type,
                'changed_at' => isset($item['changed']) && is_numeric($item['changed'])
                    ? Carbon::createFromTimestamp($item['changed'])
                    : null,
                'action' => ZenRawItem::ACTION_CU,
            ];

        $validator = Validator::make($data + $addon, [
            'type' => [
                'required',
                Rule::in(ZenRawItem::TYPES),
            ],
            'action' => [
                'required',
                Rule::in(ZenRawItem::ACTIONS),
            ],
            'zen_id' => 'required|string',
            'changed_at' => 'required',
            'data' => 'required',
        ]);

        /* @noinspection PhpUnhandledExceptionInspection */
        return $validator->passes() ? new ZenRawItem($validator->validated()) : $validator->errors();
    }
}
