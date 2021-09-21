<?php
/**
 * Настройки взаимодействия с сервисом ZenMoney.
 */

declare(strict_types=1);

$serviceUrl = \rtrim(\env('ZEN_URI'), '\\');

return [
    // Идентификационные параметры OAuth клиента
    'client' => [
        'id' => \env('ZEN_CLIENT_ID'),
        'secret' => \env('ZEN_CLIENT_SECRET'),
        'redirect' => \env('ZEN_CLIENT_REDIRECT'),
    ],
    // Список адресов, с которыми мы взаимодействуем
    'url' => [
        // Страница авторизации (введите свой логин и пароль, чтобы войти в ZenMoney)
        'authorize' => "{$serviceUrl}/oauth2/authorize/",
        // Страница, через которую можно обменять code на токен
        // @see ZenTokenManager::exchange
        'token' => "{$serviceUrl}/oauth2/token/",
        // Страница получения и отправки данных
        // @see ZenClient::diff
        'diff' => "{$serviceUrl}/v8/diff/",
    ],
];
