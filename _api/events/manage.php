<?php

require_once __DIR__ . "/../../_app/autoload.php";

//$oEvent = \Arura\Events\Event::Create([
//    "Event_Name" => "Test",
//    "Event_Description" => "ajwkdlhagwkdhawgdjaw",
//    "Event_Start_Timestamp" => 83274982,
//    "Event_End_Timestamp" => 23487,
//    "Event_Location" => "Sittard",
//    "Event_Price" => 10.11,
//    "Event_Banner" => "/awdhawd/awdkhgajw/awd.jpg",
//    "Event_Organizer_User_Id" => 1,
//    "Event_IsActive" => 0,
//    "Event_IsVisible" => 0,
//    "Event_Capacity" => 0
//]);
$oEvent = new \Arura\Events\Event(3);
$oEvent->load(true);
$oEvent->setEnd(new DateTime());

$oEvent->getCapacity();
$oEvent->getOrganizer()->getLastname();
var_dump($oEvent->getIsActive());
$oEvent->save();
var_dump($oEvent->__ToArray());