<?php

namespace Arura;

use Cz\Git\GitRepository;

class Git extends GitRepository {

    /**
     * @return NULL|string[]
     * @throws \Cz\Git\GitException
     */
    public function getStatus(){
        return $this->extractFromCommand("git status", function($value) {
            return substr($value, 0);
        });
    }

    /**
     * @param bool $force
     * @return NULL|string[]
     * @throws \Cz\Git\GitException
     */
    public function Reset($force = false){
        $this->extractFromCommand("git clean " .(($force) ? "-f": null), function($value) {
            return trim(substr($value, 1));
        });
        return $this->extractFromCommand("git reset " .(($force) ? "--hard": null), function($value) {
            return trim(substr($value, 1));
        });
    }

}