<?php
namespace Arura\SystemLogger;

use Arura\User\User;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class Notify extends AbstractProcessingHandler {

    protected function write(array $record): void
    {
        $message = "{$record["level_name"]} | {$record["channel"]}\n{$record["message"]}\n";
        $message .= ((is_int($record["User_Id"])) ? "Logged in user: " . (new User($record["User_Id"]))->getEmail() : "Guest") . "\n";
        $message .= "Ip address {$record["Request_Ip"]}";
        NotifyNeal($message,1);
    }
}