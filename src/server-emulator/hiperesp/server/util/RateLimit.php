<?php declare(strict_types=1);
namespace hiperesp\server\util;

/**
 * File-backed rate limiter. No schema migration required; safe across client data upgrades.
 */
class RateLimit {

    /**
     * @return bool true if the action is allowed, false if the limit was hit
     */
    public static function attempt(string $bucket, int $maxAttempts, int $windowSeconds): bool {
        if($maxAttempts < 1 || $windowSeconds < 1) {
            return true;
        }

        $path = self::path($bucket);
        $fh = \fopen($path, 'c+');
        if($fh === false) {
            return true;
        }

        try {
            if(!\flock($fh, \LOCK_EX)) {
                return true;
            }

            \rewind($fh);
            $raw = \stream_get_contents($fh);
            $now = \time();
            $attempts = [];
            if($raw !== false && $raw !== '') {
                $decoded = \json_decode($raw, true);
                if(\is_array($decoded)) {
                    $attempts = \array_values(\array_filter(
                        $decoded,
                        static fn($ts): bool => \is_int($ts) && $ts > ($now - $windowSeconds)
                    ));
                }
            }

            if(\count($attempts) >= $maxAttempts) {
                self::write($fh, $attempts);
                return false;
            }

            $attempts[] = $now;
            self::write($fh, $attempts);
            return true;
        } finally {
            \flock($fh, \LOCK_UN);
            \fclose($fh);
        }
    }

    public static function clear(string $bucket): void {
        $path = self::path($bucket);
        if(\file_exists($path)) {
            @\unlink($path);
        }
    }

    private static function path(string $bucket): string {
        $safe = \preg_replace('/[^a-zA-Z0-9._-]+/', '_', $bucket) ?: 'bucket';
        return \sys_get_temp_dir().\DIRECTORY_SEPARATOR.'df-ratelimit-'.$safe.'.json';
    }

    private static function write($fh, array $attempts): void {
        \ftruncate($fh, 0);
        \rewind($fh);
        \fwrite($fh, \json_encode(\array_values($attempts)));
        \fflush($fh);
    }

}
