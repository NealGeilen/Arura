<?php
use Arura\Dashboard\Page;
Page::getSmarty()->assign("aTables", \Arura\SecureAdmin\SecureAdmin::getAllTablesForUser(\NG\User\User::activeUser()));
return Page::getHtml(__DIR__);