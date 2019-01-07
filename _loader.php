<?php
$aRoots = explode('.', $_GET['_url_']);
$aPath = explode('/', $aRoots[0]);
unset($aRoots[0]);
$aRoots = array_values($aRoots);

global $aUrl;
global $aHeaders;
$aUrl = $aPath;
$aHeaders = $aRoots;

include "web-controller.php";
