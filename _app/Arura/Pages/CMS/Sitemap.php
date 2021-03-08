<?php
namespace Arura\Pages\CMS;

use Arura\Cache;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Gallery\Gallery;
use Arura\Permissions\Restrict;
use Arura\Settings\Application;
use Arura\Shop\Events\Event;
use DateTime;
use DateTimeZone;
use DOMDocument;
use Google_Exception;
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
        $this->parsePages();
    }

    public function save()
    {
        $xml = $this->toXML(true, true);
        file_put_contents(
            $this->file,
            $xml
        );
    }

    /**
     * @throws Error
     */
    private function parsePages()
    {
        /**
         * CMS Pages
         */
        $aPages = Page::getAllVisiblePages();
        foreach ($aPages as $page) {
            $lastmod = new DateTime();

            $uri    = Application::get("website", "url") . $page["Page_Url"];
            $isRoot = $page["Page_Url"] == '' || $page["Page_Url"] == '/';

            $this->urlset[] = [
                'loc'        => $uri,
                'changefreq' => Sitemap::CHANGEFREQ_MONTHLY,
                'priority'   => $isRoot ? 1 : 0.7,
                'lastmod'    => $lastmod->format(DATE_W3C)
            ];
        }

        /**
         * Event pages
         */
        $aEvents = Event::getEvents();
        foreach ($aEvents as $event) {
            $lastmod = new DateTime();

            $uri    = Application::get("website", "url") ."/event/". $event->getSlug();

            $this->urlset[] = [
                'loc'        => $uri,
                'changefreq' => Sitemap::CHANGEFREQ_YEARLY,
                'priority'   => 0.9,
                'lastmod'    => $lastmod->format(DATE_W3C)
            ];
        }


        /**
         * Gallery pages
         */
        $aPages = Gallery::getAllGalleries(true);
        foreach ($aPages as $page) {
            $lastmod = $page->getCreatedDate();

            $uri    = Application::get("website", "url") ."/album/".$page->getId();

            $this->urlset[] = [
                'loc'        => $uri,
                'changefreq' => Sitemap::CHANGEFREQ_YEARLY,
                'priority'   => 0.4,
                'lastmod'    => $lastmod->format(DATE_W3C)
            ];
        }
    }


    public static function Display(){
        $Sitemap = new self();
        $Sitemap->build();
        $xml = $Sitemap->toXML(true, true);
        header("Cache-Control: must-revalidate,max-age=" . 40*84600);
        header("Content-Type: application/xml");
        header("Content-Length: " .strlen($xml));
        header('Content-Transfer-Encoding: base64');
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 40*84600) . " GMT");
        header("Pragma: cache");
        header("Content-Disposition: inline; filename=sitemap.xml");
        echo $xml;
        exit;
    }


    public static function DisplayTxt(){
        $Sitemap = new self();
        $Sitemap->build();
        $txt = $Sitemap->toTxt();
        header("Cache-Control: must-revalidate,max-age=" . 40*84600);
        header("Content-Type: text/plain");
        header("Content-Length: " .strlen($txt));
        header('Content-Transfer-Encoding: base64');
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 40*84600) . " GMT");
        header("Pragma: cache");
        header("Content-Disposition: inline; filename=sitemap.txt");
        echo $txt;
        exit;
    }

    /**
     * @throws Error
     */
    public function build()
    {
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
                $url->appendChild($dom->createElement('lastmod', $item['lastmod']));
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


    public function toTxt()
    {
        $text = "";
        foreach ($this->urlset as $url){
            $text .="{$url['loc']} \r";
        }
        return$text;
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
        $url = Application::get("website", "url");
        if (!file_get_contents("http://www.google.com/ping?sitemap={$url}sitemap.xml")){
            throw new Error();
        }
    }
}