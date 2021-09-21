<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Managers\MscTokenManager;
use App\OAuth\MscProvider;
use DomainException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use function http_build_query;
use function redirect;

/**
 * Обслуживает авторизацию в MSC.
 */
class MscAuthController extends Controller
{
    private array $stateValidationRules = [
        'user_id' => 'required|int|gt:0',
        'firm_id' => 'required|int|gt:0',
        'redirect_to' => 'required|url',
    ];

    /**
     * Пользователь направляется на специальную страницу с
     * GET параметрами `user_id`, `firm_id` и `redirect_to`
     * Интеграция формирует state и другие параметры,
     * необходимые для авторизации, после чего направляет
     * пользователя в MSC.
     *
     * @param Request $request
     * @param MscProvider $provider
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function auth(Request $request, MscProvider $provider): RedirectResponse
    {
        $input = $this->validate($request, $this->stateValidationRules);

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        return redirect()->to($provider->getAuthorizationUrl([
            'state' => Crypt::encrypt($input),
            'scope' => [
                'view-me',
                "view-firm-{$input['firm_id']}-accounts",
                "create-firm-{$input['firm_id']}-accounts",
                "update-firm-{$input['firm_id']}-accounts",
                "delete-firm-{$input['firm_id']}-accounts",
            ],
        ]));
    }

    /**
     * В MSC происходит проверка возможности пользователя
     * получить токен доступа к указанной фирме,
     * затем отображается интерфейс с описанием
     * предоставляемых доступов, а также двумя
     * кнопками "разрешить" и "отклонить"
     * При нажатии на кнопку "разрешить" пользователь
     * будет перенаправлен на callback-uri,
     * Которая, после успешного обмена кода на токен,
     * направит его авторизоваться в ZenMoney, зашифровав
     * в state `user_id`, `firm_id` и `redirect_to` для
     * последующего связывания полученного токена
     * ZenMoney с токеном пользователя.
     *
     * @param Request $request
     * @param MscTokenManager $manager
     *
     * @return string|RedirectResponse
     *
     * @throws ValidationException
     */
    public function code(Request $request, MscTokenManager $manager): string|RedirectResponse
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
            $token = $manager->exchange($input['code'], (int)$state['user_id'], (int)$state['firm_id']);
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

        return redirect()->to(
            '/oauth/zen/auth?'.http_build_query(['state' => $input['state']]),
            303
        );
    }
}
