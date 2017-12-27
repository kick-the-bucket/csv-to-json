<?php

namespace CsvToJson\Constant;

/**
 * Class Medals.
 */
final class Medals
{
    const BRONZE_SHORT = 'b';
    const GOLD_LONG = 'gold';
    const GOLD_SHORT = 'g';
    const ONYX_LONG = 'onyx';
    const PLATINUM_LONG = 'plat';
    const PLATINUM_SHORT = 'p';
    const SILVER_LONG = 'silv';
    const SILVER_SHORT = 's';

    /**
     * @var array
     */
    public static $days = [
        self::SILVER_LONG => 10,
        self::GOLD_LONG => 20,
        self::PLATINUM_LONG => 90,
        self::ONYX_LONG => 150,
    ];
    /**
     * @var array
     */
    public static $nextMedal = [
        self::BRONZE_SHORT => self::SILVER_LONG,
        self::SILVER_SHORT => self::GOLD_LONG,
        self::GOLD_SHORT => self::PLATINUM_LONG,
        self::PLATINUM_SHORT => self::ONYX_LONG,
    ];
}
