<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelHelpers;

use Exception;
use RuntimeException;
use Symfony\Component\Process\ProcessUtils;

/**
 * Class ProcessUtls
 *
 * @package MPhpMaster\LaravelHelpers
 */
class ProcessUtls extends ProcessUtils
{
    const OS_WINDOWS = 1;
    const OS_NIX = 2;
    const OS_OTHER = 3;

    /**
     * @var string
     */
    private $command;

    /**
     * @var int
     */
    private $pid;
    private ?string $dir = "";

    /**
     * @param string $command The command to execute
     *
     * @codeCoverageIgnore
     */
    public function __construct($command = null)
    {
        $this->command = $command;
        $this->getOS();
    }

    /**
     * Returns the ID of the process.
     *
     * @return int The ID of the process
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set the process id.
     *
     * @param $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * Set the command.
     *
     * @param string|null $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return string|null
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * Set the directory.
     *
     * @param string|null $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return string|null
     */
    public function getDir(): ?string
    {
        return $this->dir;
    }

    /**
     * @return int
     */
    public function getOS()
    {
        $os = strtoupper(PHP_OS);

        if ( substr($os, 0, 3) === 'WIN' ) {
            return self::OS_WINDOWS;
        }

        if ( $os === 'LINUX' || $os === 'FREEBSD' || $os === 'DARWIN' ) {
            return self::OS_NIX;
        }

        return self::OS_OTHER;
    }

    /**
     * @param string $message Exception message if the OS is not supported
     *
     * @throws RuntimeException if the operating system is not supported by Cocur\BackgroundProcess
     *
     * @codeCoverageIgnore
     */
    protected function checkSupportingOS($message)
    {
        if ( $this->getOS() !== self::OS_NIX ) {
            throw new RuntimeException(sprintf($message, PHP_OS));
        }
    }

    /**
     * @return bool
     */
    public function isWindows()
    {
        return $this->getOS() === self::OS_WINDOWS;
    }

    /**
     * @return bool
     */
    public function isNix()
    {
        return $this->getOS() === self::OS_NIX;
    }

    /**
     * @param int         $pid PID of process to resume
     * @param string|null $command
     *
     * @return static
     */
    public static function createFromPID($pid, $command = null)
    {
        $process = new self($command);
        $process->setPid($pid);

        return $process;
    }

    /**
     * @param \App\Models\Tools\Process $model Process Model
     *
     * @return static
     */
    public static function createFromModel(\App\Models\Tools\Process $model)
    {
        $process = new self($model->getCommand());
        $process->setPid($model->getPid());

        return $process;
    }

    /**
     * @param       $command
     * @param       $log
     * @param false $append
     *
     * @return false|string
     * @deprecated test method
     */
//    private function execCommand($command, $log, $append = false)
//    {
//        $_logAppend = $append ? ">" : "";
//        if ( $this->isWindows() ) {
//            //windows
//            $handle = proc_open("start /B " . $command . " >{$_logAppend} $log", [], $foo);
//            $data = proc_get_status($handle);
//            proc_close($handle);
////            dE(
////                $data
////            );
//        } else {
//            //linux
//            return shell_exec($command . " 1>{$_logAppend} $log 2>&1 & echo $!");
//        }
//
//        return false;
//    }

    /**
     * Runs the command in a background process.
     *
     * @param string $outputFile File to write the output of the process to; defaults to /dev/null
     *                           currently $outputFile has no effect when used in conjunction with a Windows server
     * @param bool   $append - set to true if output should be appended to $outputfile
     */
    public function run($outputFile = '/dev/null', $append = false)
    {
        if ( $this->command === null ) {
            return null;
        }

        switch ($this->getOS()) {
            case self::OS_WINDOWS:
                shell_exec(sprintf('nohup %s %s %s &', $this->command, ($append) ? '>>' : '>', $outputFile));
                break;
            case self::OS_NIX:
                $this->setPid((int)shell_exec(sprintf('nohup %s %s %s 2>&1 & echo $!', $this->command, ($append) ? '>>' : '>', $outputFile)));
                break;
            default:
                throw new RuntimeException(sprintf(
                    'Could not execute command "%s" because operating system "%s" is not supported by ' .
                    'Cocur\BackgroundProcess.',
                    $this->command,
                    PHP_OS
                ));
        }
    }

    /**
     * Returns if the process is currently running.
     *
     * @return bool TRUE if the process is running, FALSE if not.
     * @noinspection ForgottenDebugOutputInspection
     */
    public function isRunning()
    {
//        $this->checkSupportingOS('Cocur\BackgroundProcess can only check if a process is running on *nix-based ' .
//            'systems, such as Unix, Linux or Mac OS X. You are running "%s".');

        try {
            if ( $this->isWindows() ) {
                $result = shell_exec(sprintf('tasklist /FI "PID eq %d"', $this->getPid()));
                $results = array_filter(explode("\n", $result));
                array_shift($results);
                array_shift($results);
            } else {
                $result = shell_exec(sprintf('ps %d 2>&1', $this->getPid()));
                $results = array_filter(explode("\n", $result));
                array_shift($results);
            }

            if ( count($results) && !preg_match('/ERROR:|ERROR: Process ID out of range/', $result) ) {
                return true;
            }
        } catch (Exception $e) {
            dE(
                $e
            );
        }

        return false;
    }

    /**
     * Stops the process.
     *
     * @return bool `true` if the processes was stopped, `false` otherwise.
     */
    public function kill()
    {
        try {
            if ( $this->isWindows() ) {
                $result = shell_exec(sprintf('taskkill /PID %d', $this->getPid()));
            } else {
                $result = shell_exec(sprintf('kill %d 2>&1', $this->getPid()));
            }
            if ( !preg_match('/No such process|ERROR:/', $result) ) {
                return true;
            }
        } catch (Exception $e) {
        }

        return false;
    }
}
