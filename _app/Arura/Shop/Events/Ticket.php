<?php

namespace Arura\Shop\Events;

use Arura\Modal;
use Arura\PDF;
use Arura\QR;

class Ticket extends Modal {



    public function getPDFTicket(){
        $oPDF = new PDF();
        $oPDF->assign("QR", QR::Create(""));
        $oPDF->SetHTMLHeader();
        $oPDF->SetHTMLFooter();

    }

}
