<?php

namespace App\Helpers;

use Carbon\Carbon;

class PeriodHelper
{
    public const PERIODS = [
        'today',
        'this_week',
        'this_month',
        'last_30_days',
        'last_12_months',
        'all',
        'custom',
    ];

    public static function isPeriod($value): bool
    {
        return is_string($value) && in_array($value, self::PERIODS, true);
    }

    private static function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value, 'America/Sao_Paulo');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public static function range(string $period, ?string $start = null, ?string $end = null): array
    {
        $now = Carbon::now('America/Sao_Paulo');

        switch ($period) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'this_week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'last_30_days':
                return [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()];
            case 'last_12_months':
                return [$now->copy()->subMonths(12)->startOfDay(), $now->copy()->endOfDay()];
            case 'custom':
                $from = self::parseDate($start)?->startOfDay();
                $to = self::parseDate($end)?->endOfDay();

                return [$from, $to];
            case 'all':
            default:
                return [null, null];
        }
    }

    /**
     * Applies a period preset or a legacy "MM-YYYY" filter to the given query's date_movement column.
     * Returns false when $date is neither a known period nor a valid "MM-YYYY" string.
     * For the "custom" period, $start and/or $end ("Y-m-d") bound the range; either may be omitted
     * for an open-ended filter, and omitting both behaves like "all".
     */
    public static function applyDateFilter($query, ?string $date, string $column = 'date_movement', ?string $start = null, ?string $end = null): bool
    {
        if (self::isPeriod($date)) {
            [$from, $to] = self::range($date, $start, $end);

            if ($from && $to) {
                $query->whereBetween($column, [$from, $to]);
            } elseif ($from) {
                $query->where($column, '>=', $from);
            } elseif ($to) {
                $query->where($column, '<=', $to);
            }

            return true;
        }

        $parts = explode('-', (string) $date);

        if (count($parts) !== 2) {
            return false;
        }

        [$month, $year] = $parts;

        if (! is_numeric($month) || ! is_numeric($year) || strlen($month) !== 2 || strlen($year) !== 4) {
            return false;
        }

        $query->whereMonth($column, $month)->whereYear($column, $year);

        return true;
    }
}
