<?php

declare(strict_types=1);

namespace App\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use function config;

/**
 * Класс, обслуживающий OAuth авторизационное взаимодействие с сервисом ZenMoney.
 *
 * @see https://oauth2-client.thephpleague.com/
 * @see https://github.com/zenmoney/ZenPlugins/wiki/ZenMoney-API
 */
class ZenProvider extends GenericProvider
{
    public function __construct()
    {
        parent::__construct([
            // The client ID assigned to you by the provider
            'clientId' => config('zen.client.id'),
            // The client password assigned to you by the provider
            'clientSecret' => config('zen.client.secret'),
            'redirectUri' => config('zen.client.redirect'),
            'urlAuthorize' => config('zen.url.authorize'),
            'urlAccessToken' => config('zen.url.token'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredOptions(): array
    {
        return [
            'urlAuthorize',
            'urlAccessToken',
        ];
    }
}
