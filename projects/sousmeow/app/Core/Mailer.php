<?php

declare(strict_types=1);

namespace SousMeow\Core;

/**
 * Outbound mail via SMTP (Hostinger) or local log driver for development.
 * Never logs credentials or full message bodies on failure.
 */
final class Mailer
{
    /**
     * @param array{html?: string, text?: string} $bodies
     */
    public static function send(string $to, string $subject, array $bodies): bool
    {
        $driver = Config::string('mail.driver', 'log');
        if ($driver === 'log') {
            return self::log($to, $subject, $bodies);
        }
        return self::smtp($to, $subject, $bodies);
    }

    /** @param array{html?: string, text?: string} $bodies */
    private static function log(string $to, string $subject, array $bodies): bool
    {
        $dir = Config::string('mail.log_dir');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
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

    /** @param array{html?: string, text?: string} $bodies */
    private static function smtp(string $to, string $subject, array $bodies): bool
    {
        $host = Config::string('mail.host');
        $port = Config::int('mail.port', 465);
        $encryption = Config::string('mail.encryption', 'ssl');
        $username = Config::string('mail.username');
        $password = Config::string('mail.password');

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

            $ehloHost = parse_url(Config::string('app.url', 'localhost'), PHP_URL_HOST) ?: 'localhost';
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

            $from = Config::string('mail.from_address');
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
        $fromName = Config::string('mail.from_name', 'Chef Meow');
        $fromAddr = Config::string('mail.from_address');
        $replyTo = Config::string('mail.reply_to', $fromAddr);
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
