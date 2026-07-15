<?php

declare(strict_types=1);

namespace Rally\Services;

/**
 * Application clock. Production returns real UTC time. Development and
 * tests may inject a fixed override so settlement and lifecycle are
 * deterministic.
 *
 * Domain services must call Clock::now() instead of date()/time()/new DateTime().
 * Creating DateTimeImmutable from an explicit stored timestamp remains fine.
 */
final class Clock
{
    private static ?\DateTimeImmutable $override = null;
    private static bool $loaded = false;

    public static function now(): \DateTimeImmutable
    {
        self::ensureLoaded();
        if (self::$override !== null) {
            return self::$override;
        }
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public static function nowUtcString(): string
    {
        return self::now()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    public static function setOverride(?\DateTimeImmutable $instant): void
    {
        if ($instant === null) {
            self::$override = null;
            self::$loaded = true;
            self::clearOverrideFile();
            return;
        }
        self::$override = $instant->setTimezone(new \DateTimeZone('UTC'));
        self::$loaded = true;
        self::persistOverride(self::$override);
    }

    public static function clearOverride(): void
    {
        self::setOverride(null);
    }

    public static function hasOverride(): bool
    {
        self::ensureLoaded();
        return self::$override !== null;
    }

    public static function override(): ?\DateTimeImmutable
    {
        self::ensureLoaded();
        return self::$override;
    }

    /** Inject a fixed clock for tests without touching the override file. */
    public static function freezeForTests(\DateTimeImmutable $instant): void
    {
        self::$override = $instant->setTimezone(new \DateTimeZone('UTC'));
        self::$loaded = true;
    }

    public static function resetForTests(): void
    {
        self::$override = null;
        self::$loaded = true;
    }

    private static function ensureLoaded(): void
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;
        $path = self::overridePath();
        if (!is_file($path)) {
            return;
        }
        $raw = trim((string) file_get_contents($path));
        if ($raw === '') {
            return;
        }
        try {
            self::$override = new \DateTimeImmutable($raw, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            self::$override = null;
        }
    }

    private static function overridePath(): string
    {
        return dirname(__DIR__, 2) . '/storage/clock_override.txt';
    }

    private static function persistOverride(\DateTimeImmutable $instant): void
    {
        $path = self::overridePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents($path, $instant->format('Y-m-d\TH:i:s\Z'));
    }

    private static function clearOverrideFile(): void
    {
        $path = self::overridePath();
        if (is_file($path)) {
            unlink($path);
        }
    }
}
