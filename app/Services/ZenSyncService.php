<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Clients\ZenClient;
use App\Jobs\ZenSaveRawItemJob;
use App\Managers\MscDataManager;
use App\Models\Integration;
use App\Models\ZenToken;
use App\Repositories\ZenTokensRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;
use function count;
use function dispatch;
use function is_array;
use function time;

class ZenSyncService
{
    private ZenTokensRepository $tokensRepository;
    private ZenClient $client;
    private MscDataManager     $mscManager;

    public function __construct(
        ZenTokensRepository $tokensRepository,
        ZenClient $client,
        MscDataManager $mscManager,
    ) {
        $this->tokensRepository = $tokensRepository;
        $this->client = $client;
        $this->mscManager = $mscManager;
    }

    /**
     * Отправляем накопленные данные из MSC, взамен получаем новые данные из ZenMoney.
     *
     * @param Integration $integration
     *
     * @throws Throwable не удалось сохранить ни один элемент в БД
     */
    public function sync(Integration $integration): void
    {
        Log::debug('Start sync', $integration->attributesToArray());

        $data = $this->mscManager->getZenDataFor(
            $integration->msc_user_id,
            $integration->msc_firm_id
        );

        $group = $integration->group;
        $lastSyncedDate = $group->last_synced_at;
        $lastSyncedAt = null === $lastSyncedDate ? 0 : $lastSyncedDate->timestamp;

        $groupId = $integration->group_id;

        $response = $this
            ->tokensRepository
            ->activeByGroupId($groupId)
            ->reduce(
                function (?array $response, ZenToken $token) use ($data, $lastSyncedAt) {
                    return $response ?? $this->sendSyncRequest($token, $data, $lastSyncedAt);
                }
            );

        if (null === $response) {
            Log::warning('Failed to load data from ZenMoney');

            return;
        }

        foreach ($response as $key => $items) {
            if (is_array($items)) {
                Log::info('Start save items from ZenMoney', [
                    'key' => $key,
                    'cnt' => count($items),
                ]);
                $type = (string)$key;

                foreach ($items as $item) {
                    if (is_array($item)) {
                        dispatch(new ZenSaveRawItemJob($groupId, $type, $item));
                    }
                }
            } else {
                Log::info('Skip saving data from ZenMoney', [
                    'key' => $key,
                    'items' => $items,
                ]);
            }
        }

        $group->last_synced_at = Carbon::createFromTimestamp(
            $response['serverTimestamp'] ?? time()
        );
        $group->save();
    }

    /**
     * Отправка запроса на синхронизацию.
     *
     * @param ZenToken $token Токен авторизации ZenMoney
     * @param array $data Информация, которую необходимо отправить в ZenMoney
     * @param int $lastSyncedAt Timestamp даты последней синхронизации
     *
     * @return array|null Ответ от сервера, если удалось провести запрос, null в противном случае
     */
    private function sendSyncRequest(ZenToken $token, array $data, int $lastSyncedAt): ?array
    {
        try {
            $diffResponse = $this->client->diff($token->access, $data, $lastSyncedAt);
            $httpStatus = $diffResponse->status();

            if (401 === $httpStatus) {
                // Mark token as invalid
                $token->deactivate()->save();
            } elseif (200 === $httpStatus) {
                $response = $diffResponse->json() ?: null;
            } else {
                Log::warning('Invalid response from ZenMoney', [
                    'token' => $token->attributesToArray(),
                    'status' => $httpStatus,
                    'body' => $diffResponse->json() ?: $diffResponse->body(),
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('Failed to load diff', [
                'token' => $token->attributesToArray(),
                'exception' => $exception,
            ]);
        }

        return $response ?? null;
    }
}
