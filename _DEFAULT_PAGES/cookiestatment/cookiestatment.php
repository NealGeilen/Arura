<?php
use Arura\Pages\Page;
$Page = new Page();
$Page->setTitle("Cookie beleid");
$Page->setPageContend(__DIR__ . "/cookiestatment.tpl");
$Page->showPage();