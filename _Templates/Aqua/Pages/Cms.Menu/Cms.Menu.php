<?php

use Arura\Pages\Page;
Page::addResourceFile('Js', '//cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js');
return Page::getHtml(__DIR__);