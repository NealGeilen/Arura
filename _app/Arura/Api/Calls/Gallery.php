<?php
namespace Arura\Api\Calls;

use Arura\Api\Handler;
use Arura\Database;
use Arura\Exceptions\Error;
use Symfony\Component\HttpFoundation\Request;

class Gallery{

    public static function create(){
        Handler::Create(["Name", "Description", "Public"], function (Request $request){
            $Gallery = \Arura\Gallery\Gallery::Create($request->request->get("Name", "Naamloos"), $request->request->get("Description", "Geen omschrijving"),$request->request->getInt("Public", 0));
            $Gallery->load();
            return $Gallery->__ToArray();
        });
    }

    public static function uploadImage($id){
        Handler::Create([], function (Request $request) use ($id){
            $Gallery = new \Arura\Gallery\Gallery($id);
            $Image = $Gallery->Upload($request->request->getInt("Cover"));
            $Image->load();
            return $Image->__ToArray();
        });
    }


    public static function SearchAlbums(){
        Handler::Create([], function (Request $request){

            $params = [
                "Name" => "%".$request->request->get("Name", ""). "%",
                "Public" => $request->request->getInt("Public", 1)
            ];
            $query = "SELECT * FROM tblGallery WHERE Gallery_Name LIKE :Name AND Gallery_Public = :Public";
            if ($request->request->has("Date")){
                $query .= " AND Gallery_CreatedDate = :Date";
                $params["Data"] = $request->request->getInt("Date",0);
            }


            switch ($request->request->get("Filter", "Date")){
                case "Name":
                    $query .= " ORDER BY Gallery_Name";
                    break;
                default:
                case "Date":
                    $query .= " ORDER BY Gallery_CreatedDate";
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
            $query .= " LIMIT 20";
            $db = new Database();
            $result = $db->fetchAll($query, $params);

            if ($db->isQuerySuccessful()){
                return $result;
            }
            throw new Error("Search failed", 500);
        });
    }

}