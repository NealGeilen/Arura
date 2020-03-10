<?php

use Arura\Dashboard\Page;
Page::addSourceScriptJs(file_get_contents(__ARURA__ROOT__ . "/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.js"));
Page::addSourceScriptCss(file_get_contents(__ARURA__ROOT__ ."/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.css"));