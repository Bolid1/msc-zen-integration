<?php

declare(strict_types=1);

namespace App\Http\Clients;

use Illuminate\Support\Facades\Http;
use function config;
use function data_get;
use function time;

/**
 * Клиент для взаимодействия с API ZenMoney.
 *
 * @see https://github.com/zenmoney/ZenPlugins/wiki/ZenMoney-API
 */
class ZenClient
{
    private string $diffUrl;

    public function __construct()
    {
        $this->diffUrl = config('zen.url.diff');
    }

    /**
     * Zenmoney DiffApi — это новая версия Zenmoney Api,
     * позволяющая производить синхронизацию пользовательских
     * данных между основным сервером и "толстыми" клиентами,
     * способными хранить собственную копию профиля
     * пользователя на устройствах,
     * не имеющих постоянного доступа к Сети.
     *
     * @param string $token Ключ авторизации
     * @param array $body Отправляемые данные (список накопившихся изменений или управляющие команды)
     * @param int|null $lastSyncedAt Дата последней синхронизации, влияет на количество получаемых в ответ данных
     *
     * @return array Ответ от сервера, {@link https://github.com/zenmoney/ZenPlugins/wiki/ZenMoney-API#Сущности-}
     */
    public function diff(string $token, array $body = [], ?int $lastSyncedAt = null): array
    {
        return Http::withHeaders(['Authorization' => "Bearer {$token}"])
                   ->post(
                       $this->diffUrl,
                       $body + [
                           'currentClientTimestamp' => time(),
                           'serverTimestamp' => $lastSyncedAt ?? 0,
                       ]
                   )
                   ->json() ?: [];
    }

    /**
     * Получаем список всех сущностей определённого типа из API ZenMoney.
     *
     * @param string $token Ключ авторизации
     * @param string $type Тип сущности (user, instrument, etc...)
     *
     * @return array Требуемая сущность
     */
    public function all(string $token, string $type): array
    {
        return data_get($this->diff($token, ['forceFetch' => [$type]], time()), $type, []);
    }
}
