<?php

declare(strict_types=1);

namespace App\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use function config;

/**
 * Класс, обслуживающий OAuth авторизационное взаимодействие с сервисом MSC.
 *
 * @see https://oauth2-client.thephpleague.com/
 */
class MscProvider extends GenericProvider
{
    public function __construct()
    {
        parent::__construct([
            // The client ID assigned to you by the provider
            'clientId' => config('msc.client.id'),
            // The client password assigned to you by the provider
            'clientSecret' => config('msc.client.secret'),
            'redirectUri' => config('msc.client.redirect'),
            'urlAuthorize' => config('msc.url.authorize'),
            'urlAccessToken' => config('msc.url.token'),
            'urlResourceOwnerDetails' => config('msc.url.owner'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * {@inheritDoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return parent::createResourceOwner($response['data'] ?? $response, $token);
    }
}
