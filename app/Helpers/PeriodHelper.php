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
    ];

    public static function isPeriod($value): bool
    {
        return is_string($value) && in_array($value, self::PERIODS, true);
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public static function range(string $period): array
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
            case 'all':
            default:
                return [null, null];
        }
    }

    /**
     * Applies a period preset or a legacy "MM-YYYY" filter to the given query's date_movement column.
     * Returns false when $date is neither a known period nor a valid "MM-YYYY" string.
     */
    public static function applyDateFilter($query, ?string $date, string $column = 'date_movement'): bool
    {
        if (self::isPeriod($date)) {
            [$from, $to] = self::range($date);

            if ($from && $to) {
                $query->whereBetween($column, [$from, $to]);
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
