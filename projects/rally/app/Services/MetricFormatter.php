<?php

declare(strict_types=1);

namespace Rally\Services;

/**
 * Centralized metric value formatting for display.
 */
final class MetricFormatter
{
    /**
     * @param array<string, mixed>|null $metric Metric type row (needs unit, display_unit, slug optional)
     */
    public static function format(int|float|null $value, ?array $metric = null, bool $withUnit = true): string
    {
        if ($value === null) {
            return '—';
        }

        $displayUnit = (string) ($metric['display_unit'] ?? '');
        $unit = (string) ($metric['unit'] ?? '');
        $slug = (string) ($metric['slug'] ?? '');

        if ($displayUnit === 'hours_minutes' || $slug === 'sleep_duration') {
            $minutes = (int) round((float) $value);
            $h = intdiv(abs($minutes), 60);
            $m = abs($minutes) % 60;
            $core = $h . 'h ' . sprintf('%02d', $m) . 'm';
            return $withUnit ? $core : $core;
        }

        $intVal = (int) round((float) $value);
        $core = number_format($intVal);

        if (!$withUnit) {
            return $core;
        }

        return match ($unit) {
            'steps' => $core . ' steps',
            'min' => $core . ' min',
            'bpm' => $core . ' bpm',
            'ms' => $core . ' ms',
            default => $unit !== '' ? $core . ' ' . $unit : $core,
        };
    }

    /** Compact board display (large scoreboard numerals without unit when space-tight). */
    public static function formatCompact(int|float|null $value, ?array $metric = null): string
    {
        if ($value === null) {
            return '—';
        }
        $displayUnit = (string) ($metric['display_unit'] ?? '');
        $slug = (string) ($metric['slug'] ?? '');
        if ($displayUnit === 'hours_minutes' || $slug === 'sleep_duration') {
            return self::format($value, $metric, false);
        }
        return number_format((int) round((float) $value));
    }

    public static function unitLabel(?array $metric): string
    {
        $displayUnit = (string) ($metric['display_unit'] ?? '');
        $slug = (string) ($metric['slug'] ?? '');
        if ($displayUnit === 'hours_minutes' || $slug === 'sleep_duration') {
            return '';
        }
        return (string) ($metric['unit'] ?? '');
    }

    public static function classificationLabel(string $classification): string
    {
        return match ($classification) {
            'health_comparison' => 'Health Comparison',
            'performance' => 'Performance',
            default => ucfirst(str_replace('_', ' ', $classification)),
        };
    }

    public static function strategyLabel(string $strategy): string
    {
        return match ($strategy) {
            'series_average' => 'Series average',
            'daily_wins' => 'Daily wins',
            default => ucfirst(str_replace('_', ' ', $strategy)),
        };
    }

    public static function directionLabel(bool $higherWins): string
    {
        return $higherWins ? 'Higher recorded value wins' : 'Lower recorded value wins';
    }

    public static function competitionTypeLabel(string $type): string
    {
        return MetricCompetitionService::competitionTypeLabel($type);
    }

    public static function formatPercentage(?float $pct): string
    {
        return BaselineCompetitionService::formatPercentage($pct);
    }
}
