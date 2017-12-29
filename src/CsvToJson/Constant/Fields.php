<?php

namespace CsvToJson\Constant;

/**
 * Class Fields.
 */
final class Fields
{
    public const AGENT = 'agent';
    public const DAYS = 'days';
    public const DEADLINE = 'deadline';
    public const INTEL = 'intel';
    public const LAT = 'lat';
    public const LNG = 'lng';
    public const MEDAL = 'medal';
    public const PORTAL = 'portal';

    public const HEADERS = [
        self::AGENT,
        self::MEDAL,
        self::PORTAL,
        self::LAT,
        self::LNG,
        self::INTEL,
        self::DAYS,
        self::DEADLINE,
    ];
}
