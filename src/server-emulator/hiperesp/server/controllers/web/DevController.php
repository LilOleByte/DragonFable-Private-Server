<?php declare(strict_types=1);
namespace hiperesp\server\controllers\web;

use hiperesp\server\controllers\Controller;
use hiperesp\server\attributes\Inject;
use hiperesp\server\attributes\Request;
use hiperesp\server\enums\Input;
use hiperesp\server\enums\Output;
use hiperesp\server\exceptions\DFException;
use hiperesp\server\services\UserService;
use hiperesp\server\util\DragonFableNinja2;
use hiperesp\server\vo\UserVO;

class DevController extends Controller {

    #[Inject] private UserService $userService;

    #[Request(
        endpoint: '/dev/',
        inputType: Input::QUERY,
        outputType: Output::HTML
    )]
    public function menu(array $input): string {
        $this->requireAdmin($input);
        $token = $this->escape((string)($input['token'] ?? ''));
        $tokenQuery = \rawurlencode((string)($input['token'] ?? ''));

        return <<<HTML
<h1>Dev</h1>
<p>Admin tools for user id 1. Pass <code>?token=...</code> from your login session.</p>
<hr>
<div style="display: flex;">
    <fieldset>
        <legend>Ninja2</legend>
        <form action="ninja2decrypt?token={$tokenQuery}">
            <button>Ninja2 Decrypt</button>
        </form>
        <form action="ninja2encrypt?token={$tokenQuery}">
            <button>Ninja2 Encrypt</button>
        </form>
    </fieldset>
</div>
HTML;
    }

    #[Request(
        endpoint: '/dev/ninja2decrypt',
        inputType: Input::FORM,
        outputType: Output::HTML
    )]
    public function ninja2decrypt(array $input): string {
        $input = \array_merge($_GET, $input);
        $this->requireAdmin($input);
        $ninja2 = new DragonFableNinja2;
        $token = $this->escape((string)($input['token'] ?? ''));

        $outputTxt = "";
        if(isset($input['input'])) {
            $outputTxt = \htmlspecialchars("{$ninja2->decrypt($input['input'])}");
        }
        return <<<HTML
        <pre>{$outputTxt}</pre>
        <form method='post'>
            <input type="hidden" name="token" value="{$token}">
            <textarea name='input'></textarea><br>
            <button>Submit</button>
        </form>
        HTML;
    }

    #[Request(
        endpoint: '/dev/ninja2encrypt',
        inputType: Input::FORM,
        outputType: Output::HTML
    )]
    public function ninja2encrypt(array $input): string {
        $input = \array_merge($_GET, $input);
        $this->requireAdmin($input);
        $ninja2 = new DragonFableNinja2;
        $token = $this->escape((string)($input['token'] ?? ''));

        $outputTxt = "";
        if(isset($input['input'])) {
            $outputTxt = \htmlspecialchars("{$ninja2->encrypt($input['input'])}");
        }
        return <<<HTML
        <pre>{$outputTxt}</pre>
        <form method='post'>
            <input type="hidden" name="token" value="{$token}">
            <textarea name='input'></textarea><br>
            <button>Submit</button>
        </form>
        HTML;
    }

    private function requireAdmin(array $input): UserVO {
        $token = (string)($input['token'] ?? '');
        if($token === '') {
            throw new DFException(DFException::INVALID_SESSION);
        }
        $user = $this->userService->auth($token);
        if($user->id !== 1) {
            throw new DFException(DFException::BAD_REQUEST);
        }
        return $user;
    }

    private function escape(string $value): string {
        return \htmlspecialchars($value, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
    }

}
