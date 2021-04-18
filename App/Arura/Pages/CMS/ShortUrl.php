<?php
namespace Arura\Pages\CMS;


use Arura\AbstractModal;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Form;
use Arura\Settings\Application;
use Arura\User\Logger;
use Exception;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

class ShortUrl extends AbstractModal {

    protected $token;
    protected $direction;

    public function __construct(string $token)
    {
        parent::__construct();
        $this->setToken($token);
    }

    /**
     * @return ShortUrl[]
     * @throws Error
     */
    public static function getAllUrls(){
        $db = new Database();
        $aList = [];
        foreach ($db->fetchAllColumn("SELECT Url_Token FROM tblShortUrl WHERE Url_Direction != '' ") as $sToken){
            $aList[] = new self($sToken);
        }
        return $aList;
    }

    /**
     * @param false $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aToken = $this -> db -> fetchRow("SELECT * FROM tblShortUrl WHERE Url_Token = ? ", [$this->getToken()]);
            if (empty($aToken)){
                throw new NotFound();
            }
            $this->setDirection($aToken["Url_Direction"]);
            $this -> isLoaded = true;
        }
    }

    /**
     * @return bool
     * @throws Error
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblShortUrl",[
                "Url_Token" => $this->token,
                "Url_Direction" => $this->direction
            ], "Url_Token");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    public static function Display($sToken = ""){
        $Url = new ShortUrl($sToken);
        $Url->load();
        $tag = Application::get("analytics google", "Tag");
        try {
            if (isset($_COOKIE["_gid"])){
                $ClientId = $_COOKIE["_gid"];
            } else {
                $ClientId = str_random(10);
            }
            if (!empty($tag)){
                $analytics = new Analytics((bool)Application::get("website", "HTTPS"));
                if (isset($_SERVER["HTTP_REFERER"])){
                    $sSource = $_SERVER["HTTP_REFERER"];
                } else {
                    $sSource = "(direct) / (none)";
                }
                $result = $analytics
                    ->setProtocolVersion('1')
                    ->setTrackingId(Application::get("analytics google", "Tag"))
                    ->setClientId($ClientId)
                    ->setDocumentPath("/r/{$Url->getToken()}")
                    ->setIpOverride($_SERVER["REMOTE_ADDR"])
                    ->setUserAgentOverride($_SERVER["HTTP_USER_AGENT"])
                    ->setDocumentReferrer($sSource)
                    ->sendPageview();
            }
        } catch (Exception $e){
            if (Application::get("arura", "Debug")){
                dd($e);
            }
        }
        if (empty($Url->getDirection())){
            $oPage = new \Arura\Pages\Page();
            $oPage->setPageContend("<section><h1 class='text-center'>Deze url is niet meer geldig</h1></section>");
            $oPage->setTitle("Onderhoud");
            $oPage->showPage(404);
        }
        redirect($Url->getDirection());
    }

    /**
     * @return array
     */
    public function __toArray(){
        $this->load(true);
        return [
            "Url_Token" => $this->getToken(),
            "Url_Direction" => $this->getDirection()
        ];
    }

    /**
     * @param string $destination
     * @return ShortUrl|false
     * @throws Error
     */
    public static function Create(string $destination){
        $db = new Database();
        $token = getHash("tblShortUrl", "Url_Token");
        $result = $db->createRecord("tblShortUrl", [
            "Url_Token" => $token,
            "Url_Direction" => $destination
        ]);
        return new self($token);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return ShortUrl
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        $this->load();
        return $this->direction;
    }

    /**
     * @param mixed $direction
     * @return ShortUrl
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    public static function getShortUrlForm(ShortUrl $shortUrl = null){
        $form = new Form("shorturl-form");
        $form->addText("Url_Direction", "Doel")->setHtmlType("url")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");

        if (!is_null($shortUrl)){
            $form->setDefaults($shortUrl->__ToArray());
        }

        $form->addSubmit("submit", "Opslaan");
        if ($form->isSuccess()){

            if (is_null($shortUrl)){
                $shortUrl = self::Create($form->getValues()->Url_Direction);
                Logger::Create(Logger::CREATE, self::class, $shortUrl->getToken());
                Flasher::addFlash("Verkorte url aangemaakt");
            } else {
                $shortUrl->setDirection($form->getValues()->Url_Direction);
                if (!$shortUrl->save()){
                    $form->addError("Opslaan mislukt");
                } else {
                    Logger::Create(Logger::UPDATE, self::class, $shortUrl->getToken());
                    Flasher::addFlash("Url opgeslagen");
                }
            }
        }
        return $form;
    }

    public function Delete(){
        $this->load();
        $this->setDirection(null);
        if ($this->save()){
            Flasher::addFlash("Url verwijderd");
            Logger::Create(Logger::DELETE, self::class, $this->getToken());
        }
    }


}