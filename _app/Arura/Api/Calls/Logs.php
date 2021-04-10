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
            //TODO
        });
    }

    public static function System(){
        Handler::Create([], function (Request $request){

            $params = [];
            $query = "SELECT * FROM tblSystemLog LEFT JOIN tblUsers tU on tblSystemLog.User_Id = tU.User_Id ";

            switch ($request->request->get("Filter", "Date")){
                default:
                case "Date":
                    $query .= " ORDER BY time";
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
            $query .= " LIMIT {$request->request->getInt("Limit", 20)}";
            if ($request->request->has("Offset")){
                $query .= " Offset {$request->request->getInt("Offset", 0)}";
            }
            $db = new Database();
            $result = $db->fetchAllColumn($query, $params);
            if ($db->isQuerySuccessful()){
                return $result;
            }
            throw new Error("Search failed", 500);
        });
    }

}