<?php

namespace App\Helpers;

use Log;
use App\Utils\LogUtil;
use Psr\Log\LoggerInterface;

/**
 * Class LogHelperService
 * @package Helper
 */
class LogHelperService
{
    /**
     * debug
     * @param string $message
     * @param array  $context
     */
    public function debug(string $message, array $context = [])
    {
        $this->getLogChannel()->debug($message, $context);
    }

    /**
     * info
     * @param string $message
     * @param array  $context
     */
    public function info(string $message, array $context = [])
    {
        $this->getLogChannel()->info($message, $context);
    }

    /**
     * notice
     * @param string $message
     * @param array  $context
     */
    public function notice(string $message, array $context = [])
    {
        $this->getLogChannel()->notice($message, $context);
    }

    /**
     * warning
     * @param string $message
     * @param array  $context
     */
    public function warning(string $message, array $context = [])
    {
        $this->getLogChannel()->warning($message, $context);
    }

    /**
     * error
     * @param string $message
     * @param array  $context
     */
    public function error(string $message, array $context = [])
    {
        $this->getLogChannel()->error($message, $context);
    }

    /**
     * critical
     * @param string $message
     * @param array  $context
     */
    public function critical(string $message, array $context = [])
    {
        $this->getLogChannel()->critical($message, $context);
    }

    /**
     * alert
     * @param string $message
     * @param array  $context
     */
    public function alert(string $message, array $context = [])
    {
        $this->getLogChannel()->alert($message, $context);
    }


    /**
     * get log channel
     * @return LoggerInterface
     */
    private function getLogChannel(): LoggerInterface
    {
        $class = $this->getPathFromBackTrace();
        if (strpos($class, 'Console') !== false) {
            return Log::channel('batch');
        } else {
            return Log::channel('app');
        }
    }

    /**
     * get path from backtrace
     * @return string
     */
    private function getPathFromBackTrace(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 13);
        for ($i = 0; $i < count($backtrace); ++$i) {
            if (isset($backtrace[$i]['class']) && !LogUtil::isClassToBeIgnored($backtrace[$i]['class'])) {
                return $backtrace[$i]['class'];
            } elseif (isset($backtrace[$i]['file']) && !LogUtil::isClassToBeIgnored($backtrace[$i]['file'])) {
                return $backtrace[$i]['file'];
            }
        }
        return '';
    }
}
