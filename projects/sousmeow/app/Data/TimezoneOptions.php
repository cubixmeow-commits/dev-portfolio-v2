<?php

declare(strict_types=1);

namespace SousMeow\Data;

/**
 * A short, readable timezone list for onboarding and preferences.
 * Full IANA lists are overwhelming on mobile; any valid zone still saves.
 */
final class TimezoneOptions
{
    /** @return list<array{id: string, label: string}> */
    public static function curated(): array
    {
        return [
            ['id' => 'UTC', 'label' => 'UTC'],
            ['id' => 'America/Los_Angeles', 'label' => 'Pacific Time (US & Canada)'],
            ['id' => 'America/Denver', 'label' => 'Mountain Time (US & Canada)'],
            ['id' => 'America/Chicago', 'label' => 'Central Time (US & Canada)'],
            ['id' => 'America/New_York', 'label' => 'Eastern Time (US & Canada)'],
            ['id' => 'America/Phoenix', 'label' => 'Arizona'],
            ['id' => 'America/Anchorage', 'label' => 'Alaska'],
            ['id' => 'Pacific/Honolulu', 'label' => 'Hawaii'],
            ['id' => 'America/Toronto', 'label' => 'Toronto'],
            ['id' => 'America/Vancouver', 'label' => 'Vancouver'],
            ['id' => 'America/Mexico_City', 'label' => 'Mexico City'],
            ['id' => 'America/Sao_Paulo', 'label' => 'São Paulo'],
            ['id' => 'Europe/London', 'label' => 'London'],
            ['id' => 'Europe/Paris', 'label' => 'Paris'],
            ['id' => 'Europe/Berlin', 'label' => 'Berlin'],
            ['id' => 'Europe/Amsterdam', 'label' => 'Amsterdam'],
            ['id' => 'Asia/Dubai', 'label' => 'Dubai'],
            ['id' => 'Asia/Kolkata', 'label' => 'India'],
            ['id' => 'Asia/Singapore', 'label' => 'Singapore'],
            ['id' => 'Asia/Tokyo', 'label' => 'Tokyo'],
            ['id' => 'Australia/Sydney', 'label' => 'Sydney'],
            ['id' => 'Pacific/Auckland', 'label' => 'Auckland'],
        ];
    }

    /**
     * Choices for a select, keeping a previously saved zone visible even if
     * it is not in the curated list.
     *
     * @return list<array{id: string, label: string}>
     */
    public static function forSelect(?string $saved = null): array
    {
        $choices = self::curated();
        if ($saved !== null && $saved !== '' && !self::isCurated($saved)) {
            if (self::isValid($saved)) {
                array_unshift($choices, ['id' => $saved, 'label' => $saved]);
            }
        }
        return $choices;
    }

    public static function isCurated(string $timezone): bool
    {
        foreach (self::curated() as $row) {
            if ($row['id'] === $timezone) {
                return true;
            }
        }
        return false;
    }

    public static function isValid(string $timezone): bool
    {
        if ($timezone === '') {
            return true;
        }
        try {
            new \DateTimeZone($timezone);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
