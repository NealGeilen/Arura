<?php

namespace Arura;

use Cz\Git\GitException;
use Cz\Git\GitRepository;

class Git extends GitRepository {

    /**
     * @return NULL|string[]
     * @throws GitException
     */
    public function getStatus(){
        return $this->extractFromCommand("git status", function($value) {
            return substr($value, 0);
        });
    }

    public function isGit(){
        return is_dir($this->repository . DIRECTORY_SEPARATOR . '.git');
    }

    public function isReadable(){
        return is_readable($this->repository.DIRECTORY_SEPARATOR . '.git'. DIRECTORY_SEPARATOR . "config");
    }

    /**
     * @param bool $force
     * @return NULL|string[]
     * @throws GitException
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