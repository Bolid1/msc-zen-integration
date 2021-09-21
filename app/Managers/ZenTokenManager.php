<?php

declare(strict_types=1);

namespace App\Managers;

use App\Http\Clients\ZenClient;
use App\Models\ZenToken;
use App\OAuth\ZenProvider;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\Log;
use Throwable;
use function collect;
use function data_get;
use function trim;

/**
 * Класс замыкает на себе управление OAuth токенами ZenMoney.
 */
class ZenTokenManager
{
    private ZenProvider     $provider;
    private ZenClient       $client;
    private ZenGroupManager $manager;

    public function __construct(ZenProvider $provider, ZenClient $client, ZenGroupManager $manager)
    {
        $this->provider = $provider;
        $this->client = $client;
        $this->manager = $manager;
    }

    /**
     * Обмениваем код на токен в БД.
     *
     * @param string $code Код, который хотим обменять
     *
     * @return ZenToken
     */
    public function exchange(string $code): ZenToken
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to exchange code', [
                'code' => $code,
                'exception' => $exception,
            ]);

            throw new DomainException('Invalid response received from Authorization Server');
        }

        $tokenInfo = $accessToken->getValues() + $accessToken->jsonSerialize();

        Log::debug('Got token', [
            'token' => $tokenInfo,
        ]);

        if ($accessToken->hasExpired()) {
            Log::error('Token already expired', [
                'code' => $code,
                'token' => $tokenInfo,
            ]);

            throw new DomainException('Token already expired');
        }

        // Получаем список пользователей
        $users = collect($this->client->all($accessToken->getToken(), 'user'));

        Log::debug('Got users', [
            'users' => $users->toArray(),
            'token' => $tokenInfo,
        ]);

        // Мы исходим из ситуации, что админ может быть только один
        $admins = $users->whereNull('parent');

        // Если администраторов несколько, либо администратора нет,
        // будем думать как поступать.
        if (1 !== $admins->count()) {
            Log::error('Unexpected zenmoney admins count', [
                'admins' => $admins->toArray(),
                'token' => $tokenInfo,
            ]);

            throw new DomainException('Unexpected admins count, please, contact us');
        }

        $adminId = trim((string)data_get($admins->first(), 'id', ''));
        if ('' === $adminId) {
            Log::error('Property "id" is empty in admin user info', [
                'admins' => $admins->toArray(),
                'token' => $tokenInfo,
            ]);

            throw new DomainException('Unexpected response from ZenMoney server');
        }

        $group = $this->manager->findOrCreateByAdmin($adminId);

        if (!$group->exists) {
            throw new DomainException('Failed to store group, please try again later');
        }

        $token = ZenToken::firstOrCreate(
            [
                'group_id' => $group->id,
            ],
            [
                'type' => $tokenInfo['token_type'] ?? 'Bearer',
                'expires_at' => Carbon::createFromTimestamp($accessToken->getExpires()),
                'access' => $accessToken->getToken(),
                'refresh' => $accessToken->getRefreshToken(),
            ],
        );

        Log::debug('Token has been created', $token->attributesToArray());

        return $token;
    }
}
