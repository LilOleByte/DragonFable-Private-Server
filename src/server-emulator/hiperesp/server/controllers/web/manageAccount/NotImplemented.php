<?php declare(strict_types=1);
namespace hiperesp\server\controllers\web\manageAccount;

trait NotImplemented {

    protected function notImplemented(): array {
        \http_response_code(501);
        return [
            'success' => false,
            'error' => 'Not implemented',
        ];
    }

}
