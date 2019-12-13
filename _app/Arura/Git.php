<?php

namespace Arura;

use Cz\Git\GitRepository;

class Git extends GitRepository {

    public function getStatus(){
        return $this->extractFromCommand("git remote update && git status ", function($value) {
            return trim(substr($value, 1));
        });
    }

    public function Reset($force = false){
        $this->extractFromCommand("git clean " .(($force) ? "-f": null), function($value) {
            return trim(substr($value, 1));
        });
        return $this->extractFromCommand("git reset " .(($force) ? "--hard": null), function($value) {
            return trim(substr($value, 1));
        });
    }

}