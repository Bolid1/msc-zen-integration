<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\MscToken;
use App\OAuth\MscProvider;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Класс замыкает на себе управление OAuth токенами MSC.
 */
class MscTokenManager
{
    private MscProvider $provider;

    public function __construct(MscProvider $provider)
    {
        $this->provider = $provider;
    }

    public function exchange(string $code, int $userId, int $firmId): MscToken
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to exchange code', [
                'code' => $code,
                'user_id' => $userId,
                'firm_id' => $firmId,
                'exception' => $exception,
            ]);

            throw new DomainException('Invalid response received from Authorization Server');
        }

        $tokenInfo = $accessToken->getValues() + $accessToken->jsonSerialize();

        Log::debug('Got token', [
            'user_id' => $userId,
            'firm_id' => $firmId,
            'token' => $tokenInfo,
        ]);

        if ($accessToken->hasExpired()) {
            Log::error('Token already expired', [
                'code' => $code,
                'user_id' => $userId,
                'firm_id' => $firmId,
                'token' => $tokenInfo,
            ]);

            throw new DomainException('Token already expired');
        }

        try {
            $owner = $this->provider->getResourceOwner($accessToken);
            Log::debug('Got owner', $owner->toArray());
        } catch (Throwable $exception) {
            Log::error('Failed to retrieve owner info', [
                'user_id' => $userId,
                'firm_id' => $firmId,
                'exception' => $exception,
            ]);

            throw new DomainException('Failed to obtain information about token owner');
        }

        if ($userId !== (int)$owner->getId()) {
            Log::error('Resource owner mismatch state', [
                'code' => $code,
                'user_id' => $userId,
                'firm_id' => $firmId,
                'token' => $tokenInfo,
                'owner' => $owner->toArray(),
            ]);

            throw new DomainException('Invalid grants provided, please contact us');
        }

        $token = MscToken::firstOrCreate(
            [
                'msc_user_id' => $userId,
                'msc_firm_id' => $firmId,
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
