<?php
namespace Arura\Shop\Events\Form;

use Arura\Modal;
use Arura\Shop\Events\Event;
use Symfony\Component\HttpFoundation\Request;

class Form extends Modal {
    protected Event $event;



    public function __construct(Event $event)
    {
        $this->setEvent($event);
        parent::__construct();
    }

    public function validateRequest(Request $request){
        $Fields = Field::getFields($this->getEvent());
        foreach ($Fields as $field){
            if (is_null($request->request->get($field->getTag(), null))){
                return false;
            }
        }
        foreach (Field::DefaultTags as $tag){
            if (is_null($request->request->get($tag, null))){
                return false;
            }
        }
        return true;

    }

    public function collectAdditionalFields(Request $request){
        $list = [];
        foreach (Field::getFields($this->getEvent()) as $field){
            $list[$field->getTag()] = $request->request->get($field->getTag());
        }
        return $list;
    }

    public function renderHTMLForm(){
        $result = "<div class='form-row'>";

        foreach (Field::getFields($this->getEvent()) as $field){
            $result .= $field->render();
        }

        $result .= "</div>";

        return $result;

    }

    public function order(Field $field, int $order){
        if ($order <= $field->getPosition()){
            //add
            $aFields = $this->db->fetchAll("SELECT Field_Id, Field_Order FROM tblEventRegistrationField WHERE Field_Event_Id = :Event_Id AND Field_Order >= :Order AND Field_Id != :Field_Id", ["Event_Id" => $this->getEvent()->getId(), "Order" => $order, "Field_Id" => $field->getId()]);
            foreach ($aFields as $aField){
                (new Field($aField["Field_Id"]))->update(($aField["Field_Order"] + 1), "Field_Order");
            }
        } else {
            //subtract
            $aFields = $this->db->fetchAll("SELECT Field_Id, Field_Order FROM tblEventRegistrationField WHERE Field_Event_Id = :Event_Id AND Field_Order <= :Order AND Field_Id != :Field_Id", ["Event_Id" => $this->getEvent()->getId(), "Order" => $order, "Field_Id" => $field->getId()]);
            foreach ($aFields as $aField){
                (new Field($aField["Field_Id"]))->update(($aField["Field_Order"] - 1), "Field_Order");
            }
        }
        return $field->update($order,"Field_Order");
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return Form
     */
    public function setEvent(Event $event): Form
    {
        $this->event = $event;
        return $this;
    }



}