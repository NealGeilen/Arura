<?php

if (isset($_GET["i"]) && !empty($_GET["i"])){
    if (\Arura\Permissions\Restrict::Validation(Rights::SHOP_EVENTS_REGISTRATION)){
        $db = new \Arura\Database();
        $aTicket = $db->fetchRow("SELECT * FROM tblEventOrderedTickets WHERE OrderedTicket_Hash = :Ticket_Hash", ["Ticket_Hash" => $_GET["i"]]);
        $db -> query("UPDATE tblEventOrderedTickets SET OrderedTicket_LastValided_Timestamp = :Time WHERE OrderedTicket_Hash = :Ticket_Hash", [
            "Time" => time(),
            "Ticket_Hash" => $_GET["i"]
        ]);
        \Arura\Dashboard\Page::getSmarty()->assign("aTicket", $aTicket);
        return \Arura\Dashboard\Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Ticket-validate.tpl");
    } else {
        return \Arura\Dashboard\Page::getHtml(__DIR__ . DIRECTORY_SEPARATOR . "Ticket-Not-allowd.tpl");
    }
} else {
    header("Location: " .\Arura\Settings\Application::get("website", "url"));
}


