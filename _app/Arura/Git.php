<?php

namespace Arura;

use Cz\Git\GitRepository;

class Git extends GitRepository {

    public function getStatus(){
        return $this->extractFromCommand("git status", function($value) {
            return trim(substr($value, 1));
        });
    }

    public function Reset($force = false){
        return $this->extractFromCommand("git reset -r " .(($force) ? "-f": null), function($value) {
            return trim(substr($value, 1));
        });
    }

}