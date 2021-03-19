<?php

namespace Arura\Shop\Events\Ticket;

use Arura\Exceptions\Error;
use Arura\QR;
use Arura\Settings\Application;
use Arura\Shop\Events\Registration;
use Arura\Shop\Payment;
use Mollie\Api\Exceptions\ApiException;
use Mpdf\HTMLParserMode;
use Mpdf\MpdfException;

class Pdf{

    protected Registration $registration;

    public function __construct(Registration $registration)
    {
        $this->setRegistration($registration);
    }


    protected function createTickets(){
        if ($this->getRegistration()->getPayment() instanceof Payment){
            //TODO Check code
            if (isset($this->getPayment()->getMetadata()["Tickets"])){
                foreach ($this->getPayment()->getMetadata()["Tickets"] as $i => $aTicket){
                    $iAmount = (int)$aTicket["Amount"];
                    unset($aTicket["Amount"]);
                    for ($x =0; $x < $iAmount; $x++){
                        $sTicket = $this->addTicket($aTicket["Ticket_Id"], (float)$aTicket["Ticket_Price"]);
                        $aTickets[$sTicket] = $aTicket;
                    }
                }
                return $aTickets;
            }
        }
        return false;
    }


    /**
     * @param $aTickets
     * @return string
     * @throws Error
     * @throws ApiException
     * @throws MpdfException
     * @throws SmartyException
     * @throws Exception
     */
    protected function GeneratePDFs(){
        $oPDF = new \Arura\PDF();
        $Tickets = OrderedTicket::getTickets($this->getRegistration());
        $oPDF->assign("aWebsite", Application::getAll()["website"]);
        $oPDF->assign("Event", $this->getRegistration()->getEvent());
        $oPDF->assign("btwPer", Application::get("plg.shop", "BtwPer"));
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css")){
            $oPDF->WriteHTML(file_get_contents(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        } else {
            $oPDF->WriteHTML(file_get_contents(__STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "style.css"), HTMLParserMode::HEADER_CSS);
        }
        $oPDF->assign("Tickets", $Tickets);
        $oPDF->assign("Payment", $this->getRegistration()->getPayment());
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl")){
            $footer = __CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl";
        } else {
            $footer = __STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "footer.tpl";
        }
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl")){
            $main = __CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl";
        } else {
            $main = __STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "main.tpl";
        }
        $oPDF->SetHTMLFooter($footer);
        if (is_file(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl")){
            $oPDF->setTemplate(__CUSTOM_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl");
        } else {
            $oPDF->setTemplate(__STANDARD_MODULES__ . "Tickets" . DIRECTORY_SEPARATOR . "factuur.tpl");
        }

        foreach ($Tickets as $sHash =>$ticket){
            $ticket->addToPdf($oPDF);
            $oPDF->SetHTMLFooter($footer);
            $oPDF->setTemplate($main);
        }
//        $oPDF->Output(__APP_ROOT__ . "/Tickets/" . $this->getRegistration()->get. ".pdf", "F");
//        return  __APP_ROOT__ . "/Tickets/" . $sHash. ".pdf";
    }




    /**
     * @return Registration
     */
    public function getRegistration(): Registration
    {
        return $this->registration;
    }

    /**
     * @param Registration $registration
     * @return Pdf
     */
    public function setRegistration(Registration $registration): Pdf
    {
        $this->registration = $registration;
        return $this;
    }





}



