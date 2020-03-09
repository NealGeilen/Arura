<?php
namespace Arura\Pages\CMS;

use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Permissions\Restrict;
use Arura\Settings\Application;
use DateTime;
use DateTimeZone;
use DOMDocument;
use Google_Client;
use Google_Exception;
use Google_Service_Webmasters;
use Rights;

class Sitemap
{


    const DEFAULT_SITEMAP = __WEB__ROOT__ . '/sitemap.xml';
    const CHANGEFREQ_ALWAYS  = 'always';
    const CHANGEFREQ_HOURLY  = 'hourly';
    const CHANGEFREQ_DAILY   = 'daily';
    const CHANGEFREQ_WEEKLY  = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY  = 'yearly';
    const CHANGEFREQ_NEVER   = 'never';

    private $file;
    private $urlset = null;
    private $tree   = null;

    public function __construct($file = Sitemap::DEFAULT_SITEMAP)
    {
        $this->file = $file;
    }

    public function load()
    {
        $xml  = simplexml_load_file($this->file, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $raw  = json_decode($json, true);
        $this->tree = null;

        $this->urlset = array_map(function ($url) {
            return [
                'loc'        => $url['loc'],
                'changefreq' => array_key_exists('changefreq', $url) ? $url['changefreq']          : null,
                'priority'   => array_key_exists('priority', $url)   ? floatval($url['priority'])  : null,
                'lastmod'    => array_key_exists('lastmod', $url)    ? date_parse($url['lastmod']) : null
            ];
        }, $raw["url"]);
    }

    public function save()
    {
        Restrict::Validation(Rights::CMS_PAGES);

        file_put_contents(
            $this->file,
            $this->toXML(true, true)
        );
    }

    /**
     * @throws Error
     */
    private function parsePages()
    {
        $aPages = Page::getAllVisiblePages();

        foreach ($aPages as $page) {
            $lastmod = new DateTime();

            $uri    = Application::get("website", "url") . $page["Page_Url"];
            $isRoot = $page["Page_Url"] == '' || $page["Page_Url"] == '/';

            $this->urlset[] = [
                'loc'        => $uri,
                'changefreq' => Sitemap::CHANGEFREQ_MONTHLY,
                'priority'   => $isRoot ? 1 : 0.7,
                'lastmod'    => $lastmod
            ];
        }
    }

    /**
     * @throws Error
     */
    public function build()
    {
        Restrict::Validation(Rights::CMS_MENU);

        $this->urlset = [];
        $this->parsePages();
    }

    public function toXML($raw = true, $pretty = false)
    {
        $dom  = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('urlset');
        $root = $dom->appendChild($root);

        $dom->createAttributeNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'xmlns');

        foreach ($this->urlset as $item) {
            $url = $dom->createElement('url');
            $url = $root->appendChild($url);

            $url->appendChild($dom->createElement('loc', $item['loc']));

            if ($item['lastmod'] !== null) {
                $item['lastmod']->setTimezone(new DateTimeZone('UTC'));
                $url->appendChild($dom->createElement('lastmod', $item['lastmod']->format(DATE_W3C)));
            }

            if ($item['priority'] !== null) {
                $url->appendChild($dom->createElement('priority', $item['priority']));
            }

            if ($item['changefreq'] !== null) {
                $url->appendChild($dom->createElement('changefreq', $item['changefreq']));
            }
        }

        $dom->formatOutput       = $pretty;
        $dom->preserveWhiteSpace = !$pretty;

        return $raw
            ? $dom->saveXML()
            : $dom;
    }

    public function getUrlSet()
    {
        return $this->urlset;
    }

    /**
     * @throws Google_Exception
     * @throws Error
     * @throws NotFound
     */
    public function submit()
    {
        Restrict::Validation(Rights::CMS_MENU);

        $client = new Google_Client();

        $client->setScopes([Google_Service_Webmasters::WEBMASTERS, Google_Service_Webmasters::WEBMASTERS_READONLY]);
        if (!is_file(__SETTINGS__ . '/gsac.json')){
            throw new NotFound("Settings file not set");
        }
        $client->setAuthConfig(__SETTINGS__ . '/gsac.json');

        $service = new Google_Service_Webmasters($client);

        foreach ($service->sites->listSites() as $site) {
            $siteUrl = $site->getSiteUrl();

            if (str_contains( Application::get("website", "url"),$siteUrl)) {
                $service->sitemaps->submit($siteUrl, $siteUrl . 'sitemap.xml');
            }
        }
    }
}