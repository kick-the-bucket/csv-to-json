<?php

namespace CsvToJson\Constant;

/**
 * Class Medals.
 */
final class Medals
{
    public const BRONZE_SHORT = 'b';
    public const GOLD_DAYS = 20;
    public const GOLD_LONG = 'gold';
    public const GOLD_SHORT = 'g';
    public const ONYX_DAYS = 150;
    public const ONYX_LONG = 'onyx';
    public const ONYX_SHORT = 'o';
    public const PLATINUM_DAYS = 90;
    public const PLATINUM_LONG = 'plat';
    public const PLATINUM_SHORT = 'p';
    public const SILVER_DAYS = 10;
    public const SILVER_LONG = 'silv';
    public const SILVER_SHORT = 's';

    /**
     * @var int[]
     */
    private const DAYS = [
        self::SILVER_LONG => self::SILVER_DAYS,
        self::GOLD_LONG => self::GOLD_DAYS,
        self::PLATINUM_LONG => self::PLATINUM_DAYS,
        self::ONYX_LONG => self::ONYX_DAYS,
    ];
    /**
     * @var string[]
     */
    private const NEXT_MEDAL = [
        self::BRONZE_SHORT => self::SILVER_LONG,
        self::SILVER_SHORT => self::GOLD_LONG,
        self::GOLD_SHORT => self::PLATINUM_LONG,
        self::PLATINUM_SHORT => self::ONYX_LONG,
        self::ONYX_SHORT => self::ONYX_LONG,
    ];

    /**
     * @param string $currentMedal
     * @param int    $currentDays
     *
     * @return int
     */
    public static function getDaysToNextMedal(string $currentMedal, int $currentDays): int
    {
        return self::getDaysForMedal(self::getNextMedal($currentMedal)) - $currentDays;
    }

    /**
     * @param string $medal
     *
     * @return string
     */
    public static function getNextMedal(string $medal): string
    {
        return self::NEXT_MEDAL[$medal];
    }

    /**
     * @param string $medal
     *
     * @return int
     */
    private static function getDaysForMedal(string $medal): int
    {
        return self::DAYS[$medal];
    }
}
