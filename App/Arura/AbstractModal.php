<?php
namespace Arura;

Abstract class AbstractModal{
    protected $isLoaded = false;
    protected $db;

    /**
     * AbstractModal constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }



}