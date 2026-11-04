<?php declare(strict_types=1);
namespace hiperesp\server\controllers\web\manageAccount;

use hiperesp\server\controllers\Controller;
use hiperesp\server\attributes\Request;
use hiperesp\server\enums\Input;
use hiperesp\server\enums\Output;

class ChangeEmailController extends Controller {

    use NotImplemented;

    #[Request(
        endpoint: '/api/manage-account/change-email/1',
        inputType: Input::JSON,
        outputType: Output::JSON
    )]
    public function changeEmail(array $input): array {
        return $this->notImplemented();
    }

    #[Request(
        endpoint: '/api/manage-account/change-email/2',
        inputType: Input::JSON,
        outputType: Output::JSON
    )]
    public function changeEmail2(array $input): array {
        return $this->notImplemented();
    }

}
