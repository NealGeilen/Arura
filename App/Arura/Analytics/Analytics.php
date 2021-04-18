<?php

namespace Arura\Analytics;

use Arura\Exceptions\NotFound;
use Google_Client;
use Google_Exception;
use Google_Service_AnalyticsReporting;
use GuzzleHttp\Client;

class Analytics{

    protected $client;
    protected $reporting;


    /**
     * @return void
     * @throws Google_Exception
     * @throws NotFound
     */
    protected function buildClient(){
        if (!is_file(__SETTINGS__ . 'gsac.json')){
            throw new NotFound("Settings file not configured");
        }
        $this->setClient(new Google_Client());
        $this->getClient()->setHttpClient(new Client(['verify' => false]));
        $this->getClient()->setApplicationName("Hello Analytics Reporting");
        $this->getClient()->setAuthConfig(__SETTINGS__ . 'gsac.json');
        $this->getClient()->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->setReporting(new Google_Service_AnalyticsReporting($this->getClient()));
    }



    /**
     * @return Google_Client
     */
    public function getClient() : Google_Client
    {
        return $this->client;
    }

    /**
     * @param Google_Client $client
     */
    public function setClient(Google_Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Google_Service_AnalyticsReporting
     */
    public function getReporting() : Google_Service_AnalyticsReporting
    {
        return $this->reporting;
    }

    /**
     * @param Google_Service_AnalyticsReporting $reporting
     */
    public function setReporting(Google_Service_AnalyticsReporting $reporting): void
    {
        $this->reporting = $reporting;
    }


}