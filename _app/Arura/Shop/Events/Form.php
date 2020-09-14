<?php
namespace Arura\Shop\Events;

use Arura\Database;
use Error;

class Form{

    public $event;
    public $db;

    public function __construct(Event $event){
        $this->event = $event;
        $this->db = new Database();
    }


    public function addField(string $type,string $tag,string $title, int $position, int $size){
        return $this->db->createRecord("tblEventRegistrationField",[
            "Field_Event_Id" => $this->event->getId(),
            "Field_Size" => $size,
            "Field_Order" => $position,
            "Field_Tag" => $tag,
            "Field_Type" => $type,
            "Field_Title" => $title
        ]);
    }

    public function update(int $fieldId, $value, string $field){
        $this->db->updateRecord("tblEventRegistrationField", [
            $field => $value,
            "Field_Id" => $fieldId
        ], "Field_Id");
        if(!$this->db->isQuerySuccessful()){
            throw new \Arura\Exceptions\Error();
        }
        return true;
    }

    public function delete(int $fieldId){
        $this->db->query("DELETE FROM tblEventRegistrationField WHERE Field_Id = :Field_Id", ["Field_Id" => $fieldId]);
        return $this->db->isQuerySuccessful();
    }

    public function getAllFields(){
        return $this->db->fetchAll("SELECT * FROM tblEventRegistrationField WHERE Field_Event_Id= :Id ORDER BY Field_Order", ["Id"=> $this->event->getId()]);
    }

    public function getField(int $fieldId){
        return $this->db->fetchRow("SELECT * FROM tblEventRegistrationField WHERE Field_Id = :Id", ["Id"=>$fieldId]);
    }

    public function order(int $fieldId, int $order){
        $aField = $this->getField($fieldId);
        if ($order <= (int)$aField["Field_Order"]){
            //add
            $aFields = $this->db->fetchAll("SELECT Field_Id, Field_Order FROM tblEventRegistrationField WHERE Field_Event_Id = :Event_Id AND Field_Order >= :Order AND Field_Id != :Field_Id", ["Event_Id" => $this->event->getId(), "Order" => $order, "Field_Id" => $fieldId]);
            foreach ($aFields as $aField){
                $this->update($aField["Field_Id"], ($aField["Field_Order"] + 1), "Field_Order");
            }
        } else {
            //subtract
            $aFields = $this->db->fetchAll("SELECT Field_Id, Field_Order FROM tblEventRegistrationField WHERE Field_Event_Id = :Event_Id AND Field_Order <= :Order AND Field_Id != :Field_Id", ["Event_Id" => $this->event->getId(), "Order" => $order, "Field_Id" => $fieldId]);
            foreach ($aFields as $aField){
                $this->update($aField["Field_Id"], ($aField["Field_Order"] - 1), "Field_Order");
            }
        }
        return $this->update($fieldId, $order,"Field_Order");
    }


}