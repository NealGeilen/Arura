<?php


namespace Arura\Pages;


interface PageEnum
{
    public function __construct($id);

    public function getPageContent();

    public function forceHTTPS();

    public function showPage();

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param mixed $Title
     */
    public function setTitle($Title);

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @param mixed $Url
     */
    public function setUrl($Url);

    /**
     * @param null $PageContend
     */
    public function setPageContend($PageContend);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param mixed $Description
     */
    public function setDescription($Description);
}