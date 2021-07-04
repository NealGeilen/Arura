<?php


namespace Arura\Api\Calls;


use Arura\Api\Handler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\SystemLogger\SystemLogger;
use Arura\User\Logger;
use Arura\User\User;
use Symfony\Component\HttpFoundation\Request;

class Logs
{

    public static function UserActions(){
        Handler::Create([], function (Request $request){
            $params = [];
            $query = "SELECT * FROM tblUserLogger ";

            switch ($request->request->get("Filter", "Date")){
                default:
                case "Date":
                    $query .= " ORDER BY Logger_Time";
                    break;

            }

            switch ($request->request->get("Order", "DESC")){
                case "ASC":
                    $query .= " ASC ";
                    break;
                default:
                case "DESC":
                    $query .= " DESC ";
                    break;
            }
            if ($request->request->has("User")){
                $query .= " WHERE Logger_User_Id = :User_Id ";
                $params["User_Id"] = $request->request->getInt("User");
            }
            if ($request->request->has("Limit")){
                $query .= " LIMIT {$request->request->getInt("Limit", 20)} ";
            }
            if ($request->request->has("Offset")){
                $query .= " Offset {$request->request->getInt("Offset", 0)} ";
            }
            $db = new Database();
            $result = $db->fetchAll($query, $params);
            if ($db->isQuerySuccessful()){
                return $result;
            }
            throw new Error("Search failed", 500);
        });
    }

    public static function System(){
        Handler::Create([], function (Request $request){

            $params = [];
            $query = "SELECT * FROM tblSystemLog LEFT JOIN tblUsers tU on tblSystemLog.User_Id = tU.User_Id ";

            if ($request->request->has("OldestRecord")){
                $query .= " WHERE time >= :time";
                $params["time"] = $request->request->getInt("OldestRecord", time());
            }

            switch ($request->request->get("Filter", "Date")){
                default:
                case "Date":
                    $query .= " ORDER BY time";
                    break;
                case "Level":
                    $query .= " ORDER BY level";
                    break;
            }

            switch ($request->request->get("Order", "DESC")){
                case "ASC":
                    $query .= " ASC ";
                    break;
                default:
                case "DESC":
                    $query .= " DESC ";
                    break;
            }
            if ($request->request->has("Limit")){
                $query .= " LIMIT {$request->request->getInt("Limit", 20)} ";
            }
            if ($request->request->has("Offset")){
                $query .= " Offset {$request->request->getInt("Offset", 0)} ";
            }
            $db = new Database();
            $result = $db->fetchAll($query, $params);
            if ($db->isQuerySuccessful()){
                return $result;
            }
            throw new Error("Search failed", 500);
        });
    }

}