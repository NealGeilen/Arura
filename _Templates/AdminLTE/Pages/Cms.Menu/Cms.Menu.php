<?php

use Arura\Dashboard\Page;
Page::addResourceFile('Js', '//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js');
Page::addResourceFile('Css', '//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css');
return Page::getHtml(__DIR__);