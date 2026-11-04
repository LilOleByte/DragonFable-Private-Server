<?php declare(strict_types=1);
namespace hiperesp\server\util;

/**
 * Password hashing for accounts.
 * Uses Argon2id for new hashes; password_verify still accepts older bcrypt hashes
 * so existing players keep working across upgrades.
 */
final class Password {

    public static function hash(string $password): string {
        if(!\defined('PASSWORD_ARGON2ID')) {
            throw new \RuntimeException('PASSWORD_ARGON2ID is not available in this PHP build');
        }
        return \password_hash($password, \PASSWORD_ARGON2ID);
    }

    public static function verify(string $password, string $hash): bool {
        return \password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool {
        if(!\defined('PASSWORD_ARGON2ID')) {
            return false;
        }
        return \password_needs_rehash($hash, \PASSWORD_ARGON2ID);
    }

}
