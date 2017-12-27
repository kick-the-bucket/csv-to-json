<?php

namespace CsvToJson\Constant;

/**
 * Class Alerts.
 */
final class Alerts
{
    const DESTROY = 'DestroyPortalAlert';
    const VIRUS = 'UseVirusPortalAlert';
    const GOTO = 'GotoPortalAlert';

    /**
     * @param int $number
     *
     * @return string
     */
    public static function getAlertByNumber(int $number): string
    {
        switch (true) {
            case $number <= 15:
                return self::VIRUS;
            case $number <= 30:
                return self::VIRUS;
            default:
                return self::GOTO;
        }
    }
}
