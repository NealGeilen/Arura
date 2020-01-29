<?php
namespace Arura;

Abstract class Modal{
    protected $isLoaded = false;
    protected $db;

    /**
     * Modal constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
    }



}