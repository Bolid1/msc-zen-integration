<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Managers\ZenTokenManager;
use App\Models\Integration;
use App\OAuth\ZenProvider;
use DomainException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use function redirect;

/**
 * Обслуживает авторизацию в ZenMoney.
 */
class ZenAuthController extends Controller
{
    private array $stateValidationRules = [
        'state' => 'required_without:user_id,firm_id,redirect_to|string|min:1',
        'user_id' => 'required_without:state|int|gt:0',
        'firm_id' => 'required_without:state|int|gt:0',
        'redirect_to' => 'required_without:state|url',
    ];

    /**
     * Которая, после успешного обмена кода на токен, направит его авторизоваться
     * в ZenMoney, зашифровав в state user_id, firm_id и redirect_to
     * для последующего связывания полученного токена
     * ZenMoney с токеном пользователя.
     *
     * @param Request $request
     * @param ZenProvider $provider
     *
     * @return string|RedirectResponse
     *
     * @throws ValidationException
     */
    public function auth(Request $request, ZenProvider $provider): string|RedirectResponse
    {
        $input = $this->validate($request, $this->stateValidationRules);
        if (isset($input['state'])) {
            try {
                $input = $this
                    ->getValidationFactory()
                    ->make(Crypt::decrypt($input['state']), $this->stateValidationRules)
                    ->validate();
            } catch (DecryptException $exception) {
                Log::error('Failed to decrypt state', [
                    'input' => $input,
                    'exception' => $exception,
                ]);

                return 'Invalid state provided: cannot decrypt';
            }
        }

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        return redirect()->to($provider->getAuthorizationUrl([
            'state' => Crypt::encrypt($input),
        ]));
    }

    /**
     * После ввода своего логина и пароля от ZenMoney,
     * пользователь будет перенаправлен на
     * callback-uri для ZenMoney,
     * Скрипт этой страницы:
     * - обменивает код ZenMoney на токен
     * - получает информацию о пользователях
     * - при необходимости создаёт или обновляет группу
     * - при необходимости создаёт или обновляет интеграцию
     * - перенаправляет пользователя на `redirect_to` страницу.
     *
     * @param Request $request
     * @param ZenTokenManager $manager
     *
     * @return string|RedirectResponse
     *
     * @throws ValidationException
     */
    public function code(Request $request, ZenTokenManager $manager): string|RedirectResponse
    {
        $input = $this->validate($request, [
            'code' => 'required|string|min:1',
            'state' => 'required|string|min:1',
        ]);

        Log::debug('Start exchange code', $input);

        try {
            $state = $this
                ->getValidationFactory()
                ->make(Crypt::decrypt($input['state']), $this->stateValidationRules)
                ->validate();
        } catch (DecryptException $exception) {
            Log::error('Failed to decrypt state', [
                'input' => $input,
                'exception' => $exception,
            ]);

            return 'Invalid state provided: cannot decrypt';
        }

        try {
            $token = $manager->exchange($input['code']);
        } catch (DomainException $exception) {
            return $exception->getMessage();
        }

        if (!$token->exists) {
            Log::critical('Failed to save token', [
                'input' => $input,
                'token' => $token->attributesToArray(),
            ]);

            return 'Application general error occurred, please try again later.';
        }

        Integration::updateOrCreate([
            'msc_user_id' => $state['user_id'],
            'msc_firm_id' => $state['firm_id'],
            'group_id' => $token->group_id,
        ]);

        return redirect()->to($state['redirect_to'], 303);
    }
}
