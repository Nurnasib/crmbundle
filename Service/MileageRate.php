<?php

namespace Terminalbd\CrmBundle\Service;

/**
 * Motorcycle mileage rate (BDT per km), effective-dated.
 * Add a new line to RATES for a future change; keep older lines so past months keep their rate.
 */
class MileageRate
{
    /** effective-from date => rate. Keep ascending by date. */
    const RATES = [
        '2000-01-01' => 3.5,
        '2026-10-01' => 4.5,
    ];

    const DEFAULT_RATE = 3.5;

    /**
     * Rate in force on the given date.
     *
     * @param \DateTimeInterface|string|null $date null = today
     */
    public static function rateFor($date = null): float
    {
        if ($date instanceof \DateTimeInterface) {
            $d = $date->format('Y-m-d');
        } elseif ($date) {
            $d = (new \DateTime($date))->format('Y-m-d');
        } else {
            $d = (new \DateTime())->format('Y-m-d');
        }

        $rate = self::DEFAULT_RATE;
        foreach (self::RATES as $from => $r) {
            if ($d >= $from) {
                $rate = $r;
            }
        }

        return (float) $rate;
    }

    /** km x rate for the given date. */
    public static function amount($km, $date = null): float
    {
        return (float) $km * self::rateFor($date);
    }
}
