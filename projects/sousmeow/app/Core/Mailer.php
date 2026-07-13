<?php

declare(strict_types=1);

namespace SousMeow\Core;

/**
 * Outbound mail via SMTP (Hostinger) or local log driver for development.
 * Never logs credentials or full message bodies on failure.
 *
 * Reads mail.* from config.php when present, otherwise falls back to .env
 * so production hosts with an older config.php still work.
 */
final class Mailer
{
    /**
     * @param array{html?: string, text?: string} $bodies
     */
    public static function send(string $to, string $subject, array $bodies): bool
    {
        $driver = self::setting('driver', 'MAIL_DRIVER', 'log');
        if ($driver === 'log') {
            return self::log($to, $subject, $bodies);
        }
        return self::smtp($to, $subject, $bodies);
    }

    public static function driver(): string
    {
        return self::setting('driver', 'MAIL_DRIVER', 'log');
    }

    /** @param array{html?: string, text?: string} $bodies */
    private static function log(string $to, string $subject, array $bodies): bool
    {
        $dir = self::logDir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            error_log('SousMeow mail: cannot create log directory: ' . $dir);
            return false;
        }

        $text = $bodies['text'] ?? strip_tags($bodies['html'] ?? '');
        $eml = self::headers($to, $subject, 'text/plain; charset=utf-8')
            . "\r\n"
            . $text
            . "\r\n";

        $file = $dir . '/' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.eml';
        return file_put_contents($file, $eml) !== false;
    }

    private static function logDir(): string
    {
        $dir = Config::string('mail.log_dir');
        if ($dir !== '') {
            return $dir;
        }
        return dirname(__DIR__, 2) . '/storage/mail';
    }

    /** @param array{html?: string, text?: string} $bodies */
    private static function smtp(string $to, string $subject, array $bodies): bool
    {
        $host = self::setting('host', 'SMTP_HOST', 'smtp.hostinger.com');
        $port = (int) self::setting('port', 'SMTP_PORT', '465');
        $encryption = self::setting('encryption', 'SMTP_ENCRYPTION', 'ssl');
        $username = self::setting('username', 'SMTP_USERNAME');
        $password = self::setting('password', 'SMTP_PASSWORD');

        if ($host === '' || $username === '' || $password === '') {
            error_log('SousMeow mail: SMTP not configured (missing host, username, or password).');
            return false;
        }

        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $socket = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
        if ($socket === false) {
            error_log('SousMeow mail: SMTP connection failed.');
            return false;
        }

        try {
            stream_set_timeout($socket, 15);
            self::expect($socket, [220]);

            $appUrl = Config::string('app.url', Config::string('app.base_url', Env::get('APP_URL', 'localhost')));
            $ehloHost = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';
            self::command($socket, 'EHLO ' . $ehloHost, [250]);

            if ($encryption === 'tls') {
                self::command($socket, 'STARTTLS', [220]);
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('STARTTLS failed');
                }
                self::command($socket, 'EHLO ' . $ehloHost, [250]);
            }

            self::command($socket, 'AUTH LOGIN', [334]);
            self::command($socket, base64_encode($username), [334]);
            self::command($socket, base64_encode($password), [235]);

            $from = self::setting('from_address', 'MAIL_FROM_ADDRESS', $username);
            self::command($socket, 'MAIL FROM:<' . $from . '>', [250]);
            self::command($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::command($socket, 'DATA', [354]);

            $html = $bodies['html'] ?? null;
            $text = $bodies['text'] ?? strip_tags((string) $html);
            $contentType = $html !== null
                ? 'multipart/alternative; boundary="sousmeow-boundary"'
                : 'text/plain; charset=utf-8';

            $body = self::headers($to, $subject, $contentType, true);
            if ($html !== null) {
                $body .= "--sousmeow-boundary\r\n"
                    . "Content-Type: text/plain; charset=utf-8\r\n\r\n"
                    . self::dotStuff($text) . "\r\n"
                    . "--sousmeow-boundary\r\n"
                    . "Content-Type: text/html; charset=utf-8\r\n\r\n"
                    . self::dotStuff($html) . "\r\n"
                    . "--sousmeow-boundary--\r\n";
            } else {
                $body .= "\r\n" . self::dotStuff($text) . "\r\n";
            }
            $body .= "\r\n.\r\n";

            fwrite($socket, $body);
            self::expect($socket, [250]);
            self::command($socket, 'QUIT', [221]);
            return true;
        } catch (\Throwable $e) {
            error_log('SousMeow mail: send failed — ' . $e->getMessage());
            return false;
        } finally {
            fclose($socket);
        }
    }

    private static function headers(string $to, string $subject, string $contentType, bool $forSmtp = false): string
    {
        $fromName = self::setting('from_name', 'MAIL_FROM_NAME', 'Chef Meow');
        $fromAddr = self::setting('from_address', 'MAIL_FROM_ADDRESS', self::setting('username', 'SMTP_USERNAME'));
        $replyTo = self::setting('reply_to', 'MAIL_REPLY_TO', $fromAddr);
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $lines = [
            'From: ' . $encodedFromName . ' <' . $fromAddr . '>',
            'Reply-To: ' . $replyTo,
            'To: ' . $to,
            'Subject: ' . $encodedSubject,
            'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000',
            'MIME-Version: 1.0',
            'Content-Type: ' . $contentType,
            'X-Mailer: SousMeow',
        ];

        if ($forSmtp) {
            return implode("\r\n", $lines) . "\r\n";
        }

        return implode("\r\n", $lines) . "\r\n";
    }

    private static function setting(string $configKey, string $envKey, string $default = ''): string
    {
        $fromConfig = Config::get('mail.' . $configKey);
        if ($fromConfig !== null && (string) $fromConfig !== '') {
            return (string) $fromConfig;
        }
        $fromEnv = Env::get($envKey, '');
        if ($fromEnv !== '') {
            return $fromEnv;
        }
        return $default;
    }

    /** @param resource $socket */
    private static function command($socket, string $command, array $okCodes): void
    {
        fwrite($socket, $command . "\r\n");
        self::expect($socket, $okCodes);
    }

    /** @param resource $socket */
    private static function expect($socket, array $okCodes): void
    {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $okCodes, true)) {
            throw new \RuntimeException('SMTP unexpected response: ' . trim($response));
        }
    }

    private static function dotStuff(string $body): string
    {
        return preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $body)) ?? $body;
    }
}
