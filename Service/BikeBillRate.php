<?php

namespace Terminalbd\CrmBundle\Service;

/**
 * Motorcycle mobil and servicing bill amounts, effective-dated.
 *
 * mobil bill      : paid per 1,000 km crossing. Rate depends on the bike class (high cc or not).
 * servicing bill  : paid per 2,000 km crossing. Same for every bike.
 * maintenance bill is NOT handled here (still a flat 500 per 1,000 km in the ingest code).
 *
 * To change a rate later, add a new line with its effective-from date. Keep older lines so
 * past months keep their amounts.
 */
class BikeBillRate
{
    /** effective-from date => [normal bike, high cc bike]. Keep ascending by date. */
    const MOBIL = [
        '2000-01-01' => [500, 500],
        '2026-10-01' => [900, 1000],
    ];

    /** effective-from date => amount. Keep ascending by date. */
    const SERVICING = [
        '2000-01-01' => 500,
        '2026-10-01' => 1000,
    ];

    /** Mobil bill for ONE 1,000 km crossing. */
    public static function mobilFor($date, bool $highCc = false): float
    {
        $pair = self::pick(self::MOBIL, $date);

        return (float) $pair[$highCc ? 1 : 0];
    }

    /** Servicing bill for ONE 2,000 km crossing. */
    public static function servicingFor($date): float
    {
        return (float) self::pick(self::SERVICING, $date);
    }

    /**
     * Value from the last entry whose effective-from date is on or before $date.
     *
     * @param \DateTimeInterface|string|null $date null = today
     */
    private static function pick(array $map, $date)
    {
        if ($date instanceof \DateTimeInterface) {
            $d = $date->format('Y-m-d');
        } elseif ($date) {
            $d = (new \DateTime($date))->format('Y-m-d');
        } else {
            $d = (new \DateTime())->format('Y-m-d');
        }

        $value = reset($map);
        foreach ($map as $from => $v) {
            if ($d >= $from) {
                $value = $v;
            }
        }

        return $value;
    }
}
