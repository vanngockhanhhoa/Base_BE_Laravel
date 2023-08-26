<?php

namespace App\Utils;

/**
 * Class LogUtil
 * @package App\Utils
 */
class LogUtil
{
    protected static $skipClasses = ['Illuminate', 'Monolog', 'TraceLogLineFormatter', 'LogHelperService'];

    /**
     * is class to be ignored
     * @param string $classOrFile
     * @return bool
     */
    public static function isClassToBeIgnored(string $classOrFile): bool
    {
        foreach (self::$skipClasses as $skipClass) {
            if (strpos($classOrFile, $skipClass) !== false) {
                return true;
            }
        }
        return false;
    }
}
