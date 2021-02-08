<?php
namespace Arura\SystemLogger;

use Arura\Database;
use Arura\Sessions;
use Arura\User\User;
use Exception;
use Monolog\ErrorHandler;
use Monolog\Logger;

class SystemLogger{
    const Payment = "Payment";
    const Api =  "Api";
    const Settings = "Settings";
    const DashBoard = "Dashboard";
    const Website = "Website";
    const System = "System";
    const Addon = "Addon";
    const Webhook = "Webhook";
    const Security = "Security";

    protected static $Loggers  = [];

    public const Levels = [
        Logger::DEBUG     => 'DEBUG',
        Logger::INFO      => 'INFO',
        Logger::NOTICE    => 'NOTICE',
        Logger::WARNING   => 'WARNING',
        Logger::ERROR     => 'ERROR',
        Logger::CRITICAL  => 'CRITICAL',
        Logger::ALERT     => 'ALERT',
        Logger::EMERGENCY => 'EMERGENCY',
    ];

    protected static function getLogger(string $LoggerType = "")
    {
        if (isset(self::$Loggers[$LoggerType])){
            return self::$Loggers[$LoggerType];
        }
        $log = new Logger($LoggerType);
        self::AddHandlers($log);
        self::$Loggers[$LoggerType] = $log;
        return $log;
    }

    public static function addRecord(string $LoggerType = "", int $LogeLevel =0 ,string  $message = "") : bool
    {
        return self::getLogger($LoggerType)->addRecord($LogeLevel, $message);
    }

    /**
     * @param string $LoggerType
     * @param Exception $exception
     * @return bool
     */
    public static function AddException(string $LoggerType, Exception $exception) : bool
    {
        return self::getLogger($LoggerType)->addRecord(self::HttpCodeToLogLevel($exception->getCode()), "{$exception->getMessage()} {$exception->getFile()}:{$exception->getLine()} {$exception->getCode()}");
    }

    public static function ErrorHandler(string $LoggerType = self::System)
    {
        return ErrorHandler::register(self::getLogger($LoggerType), [E_ALL]);
    }

    public static function HttpCodeToLogLevel(int $code) : string
    {
        $lvl = Logger::DEBUG;

        if ($code >= 200 && $code < 300){
            $lvl = Logger::INFO;
        }

        if ($code >=400 && $code< 500){
            $lvl = Logger::NOTICE;
        }

        if ($code >= 500 && $code < 600){
            $lvl = Logger::CRITICAL;
        }

        if ($code === 0){
            $lvl = Logger::EMERGENCY;
        }
        return $lvl;
    }


    protected static function AddHandlers(Logger $logger){
        $mySQLHandler = new MySQLHandler(Database::getConnection(), "tblSystemLog",
            [
                "User_Id",
                "Session_Id",
                "Requested_Url",
                "Request_Ip",
            ]);
        $logger->pushHandler($mySQLHandler);
        $logger->pushHandler(new Notify(Logger::WARNING));
        $logger->pushProcessor(function ($context){
            $userId = User::activeUser();
            if (!is_null($userId)){
                $userId = $userId->getId();
            } else {
                $userId = 0;
            }
           $result = array_merge([
               "User_Id" => $userId,
               "Session_Id" => Sessions::getSessionId(),
               "Request_Ip" => $_SERVER["REMOTE_ADDR"],
               "Requested_Url"=> $_SERVER["REQUEST_URI"],
           ], $context);
            return$result;
        });
    }
}