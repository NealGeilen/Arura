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
            return $Gallery->serialize();
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
            $query = "SELECT Gallery_Id FROM tblGallery WHERE Gallery_Name LIKE :Name AND Gallery_Public = :Public";
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
            $result = $db->fetchAllColumn($query, $params);
            $aGalleries = [];
            if ($db->isQuerySuccessful()){
                foreach ($result as $sId){
                    $aGalleries[] = (new \Arura\Gallery\Gallery($sId))->serialize();
                }
                return$aGalleries ;
            }
            throw new Error("Search failed", 500);
        });
    }


    public static function RandomAlbums(){
        Handler::Create([], function (Request $request){
            $query = "SELECT Gallery_Id FROM tblGallery ORDER BY RAND() ";

            $query .= " LIMIT 1";
            $db = new Database();
            $result = $db->fetchRow($query);

            if ($db->isQuerySuccessful()){
                $Gallery = new \Arura\Gallery\Gallery($result["Gallery_Id"]);
                return $Gallery->serialize();
            }
            throw new Error("Random failed", 500);
        });
    }

}