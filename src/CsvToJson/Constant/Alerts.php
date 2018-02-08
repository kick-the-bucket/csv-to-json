<?php

namespace CsvToJson\Constant;

/**
 * Class Alerts.
 */
final class Alerts
{
    private const DECAY = 'LetDecayPortalAlert';
    private const DESTROY = 'DestroyPortalAlert';
    private const FARM = 'FarmPortalAlert';
    private const GOTO = 'GotoPortalAlert';
    private const LINK = 'CreateLinkAlert';
    private const UPGRADE = 'UpgradePortalAlert';
    private const VIRUS = 'UseVirusPortalAlert';

    /**
     * @param int $days
     *
     * @return string
     */
    public static function getAlertByDays(int $days): string
    {
        switch (true) {
            case $days <= 0:
                return self::DECAY;
            case $days <= 15:
                return self::VIRUS;
            case $days <= 30:
                return self::DESTROY;
            default:
                return self::GOTO;
        }
    }
}
