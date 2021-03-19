<?php
namespace Arura\Shop\Events\Form;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Modal;
use Arura\Shop\Events\Event;
use Exception;

class Field extends Modal {

    protected int $id;
    protected Event $event;
    protected int $size;
    protected int $position;
    protected string $tag;
    protected string $type;
    protected string $title;
    protected string $value;
    protected array $attributes;

    const DefaultTags = ["firstname", "lastname", "email", "tel"];

    const Field_Types = [
        "text" => [
            "name" => "Tekst"
        ],
        "date" => [
            "name" => "Datum"
        ],
        "time" => [
            "name" => "Tijd"
        ],
        "checkbox" => [
            "name" => "checkbox"
        ],
        "email" => [
            "name" => "Email"
        ],

    ];

    public function __construct(int $id)
    {
        $this->setId($id);
        parent::__construct();
    }


    /**
     * @param bool $force
     * @throws Exception
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aField = $this -> db -> fetchRow("SELECT * FROM tblEventRegistrationField WHERE Field_Id = :Id ", ["Id" => $this -> getId()]);
            if (empty($aField)){
                throw new Error("Field not found", 404);
            }
            $this->setEvent(new Event($aField["Field_Event_Id"]))
                ->setTitle($aField["Field_Title"])
                ->setPosition($aField["Field_Order"])
                ->setValue((string)$aField["Field_Value"])
                ->setSize($aField["Field_Size"])
                ->setType($aField["Field_Type"])
                ->setTag($aField["Field_Tag"]);
            $this -> isLoaded = true;
        }
    }


    public static function addField(Event $event,string $type,string $tag,string $title, int $position, int $size, string $value = null){
        $db = new Database();
        $id =  $db->createRecord("tblEventRegistrationField",[
            "Field_Event_Id" => $event->getId(),
            "Field_Size" => $size,
            "Field_Order" => $position,
            "Field_Tag" => $tag,
            "Field_Type" => $type,
            "Field_Title" => $title,
            "Field_Value" => $value,
        ]);
        return new self($id);
    }

    /**
     * @param Event $event
     * @return Field[]
     * @throws Error
     */
    public static function getFields(Event $event){
        $db = new Database();
        $fieldIds = $db->fetchAllColumn("SELECT Field_Id FROM tblEventRegistrationField WHERE Field_Event_Id = :Event_Id ORDER BY Field_Order", ["Event_Id" => $event->getId()]);
        $results = [];
        foreach ($fieldIds as $fieldId){
            $results[] = new Field($fieldId);
        }
        return $results;
    }

    public function __ToArray(): array
    {
        return [
            "Field_Id" => $this->getId(),
            "Field_Event_Id" => $this->getEvent()->getId(),
            "Field_Size" => $this->getSize(),
            "Field_Order" => $this->getPosition(),
            "Field_Tag" => $this->getTag(),
            "Field_Type" => $this->getType(),
            "Field_Title" => $this->getTitle(),
            "Field_Value" => $this->getValue(),
        ];

    }

    public function __toString()
    {
        return (string)$this->getId();
    }


    public static function getForm(Event $event,Field $field = null){
        $types = [];
        foreach (self::Field_Types as $name => $options){
            $types[$name] = $options["name"];
        }
        $form = new \Arura\Form("field-form-$field", \Arura\Form::OneColumnRender);
        $form->addText("Field_Title", "Titel")
            ->addRule(\Arura\Form::REQUIRED, "Dit veld is verplicht");
        $form->addText("Field_Tag", "Tag")
            ->addRule(\Arura\Form::REQUIRED, "Dit veld is verplicht");
        $form->addTextArea("Field_Value", "Waarde")
            ->addRule(\Arura\Form::REQUIRED, "Dit veld is verplicht");
        $form->addSelect("Field_Type", "Type")
            ->setItems($types)
            ->addRule(\Arura\Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Opslaan");


        if ($field instanceof Field){
            $form->addHidden("Field_Id")
                ->addRule(\Arura\Form::REQUIRED, "Dit veld is verplicht");
            $form->setDefaults($field->__ToArray());
        }


        if ($form->isSuccess()){
            $data = $form->getValues("array");
            if ($field instanceof Field){
                $db = new Database();
                $db->updateRecord("tblEventRegistrationField", $data, "Field_Id");
                if ($db->isQuerySuccessful()){
                    $form->addError("Opslaan mislukt");
                }
            } else {
                self::addField($event, $data["Field_Type"], $data["Field_Tag"],$data["Field_Title"], count(self::getFields($event)), 6);
            }
        }
        return $form;
    }

    public function render($isDashboard = false){
        $result = "<div class='col-md-{$this->getSize()} field' data-size='{$this->getSize()}' data-id='{$this->getId()}'>";
        $result.= "<div class='form-group'>";
        if ($isDashboard){
            $result.="<div class='form-group-overlay'>";

            $result .= '<div class="btn-group">
<span class="btn btn-secondary Field-Position-Handler">
                        <i class="fas fa-arrows-alt"></i>
                    </span>
                    <button class="btn btn-secondary" onclick="Builder.Field.Delete($(this).parents(\'.field\'))">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <button class="btn btn-secondary" onclick="Builder.Field.Edit($(this).parents(\'.field\'))">
                        <i class="fas fa-pen"></i>
                    </button>
</div>';

            $result .= '<span class="btn btn-secondary Field-Width-Control ui-resizable-handle ui-resizable-e"><i class="fas fa-arrows-alt-h"></i></span>';

            $result.="</div>";
        }
        $result .= "<label for='#form-{$this->getEvent()->getId()}-{$this->getTag()}'>{$this->getTitle()}</label>";

        switch ($this->getType()){
            default:
                $result .= "<input type='{$this->getType()}' name='{$this->getTag()}' id='form-{$this->getEvent()->getId()}-{$this->getTag()}' class='form-control' required value='{$this->getValue()}'>";
                break;
        }
        $result .= "<div class='help-block with-errors'></div>";

        $result .="</div>";
        $result .="</div>";


        return $result;

    }

    /**
     * @param $value
     * @param string $field
     * @return bool
     * @throws Error
     */
    public function update($value, string $field){
        switch ($field){
            case "Field_Tag":
                if (in_array($value, [""])){
                    return false;
                }
                break;
        }
        $this->db->updateRecord("tblEventRegistrationField", [
            $field => $value,
            "Field_Id" => $this->getId()
        ], "Field_Id");
        return $this->db->isQuerySuccessful();
    }

    public function delete(){
        $this->db->query("DELETE FROM tblEventRegistrationField WHERE Field_Id = :Field_Id", ["Field_Id" => $this->getId()]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Field
     */
    public function setId(int $id): Field
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        $this->load();
        return $this->event;
    }

    /**
     * @param Event $event
     * @return Field
     */
    public function setEvent(Event $event): Field
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        $this->load();
        return $this->size;
    }

    /**
     * @param int $size
     * @return Field
     */
    public function setSize(int $size): Field
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        $this->load();
        return $this->position;
    }

    /**
     * @param int $position
     * @return Field
     */
    public function setPosition(int $position): Field
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        $this->load();
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return Field
     */
    public function setTag(string $tag): Field
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        $this->load();
        return $this->type;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function setType(string $type): Field
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $this->load();
        return $this->title;
    }

    /**
     * @param string $title
     * @return Field
     */
    public function setTitle(string $title): Field
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        $this->load();
        return $this->value;
    }

    /**
     * @param string $value
     * @return Field
     */
    public function setValue(string $value): Field
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        $this->load();
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return Field
     */
    public function setAttributes(array $attributes): Field
    {
        $this->attributes = $attributes;
        return $this;
    }




}