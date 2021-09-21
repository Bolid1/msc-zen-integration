<?php
/**
 * Настройки взаимодействия с сервисом MSC.
 */

declare(strict_types=1);

$serviceUrl = \rtrim(\env('MSC_URI'), '\\');

return [
    // Идентификационные параметры OAuth клиента
    'client' => [
        'id' => \env('MSC_CLIENT_ID'),
        'secret' => \env('MSC_CLIENT_SECRET'),
        'redirect' => \env('MSC_CLIENT_REDIRECT'),
    ],
    // Список адресов, с которыми мы взаимодействуем
    'url' => [
        'authorize' => "{$serviceUrl}/oauth/authorize",
        'token' => "{$serviceUrl}/api/oauth/token",
        'owner' => "{$serviceUrl}/api/users/me",
    ],
];
