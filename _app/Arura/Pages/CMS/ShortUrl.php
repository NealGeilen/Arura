<?php
namespace Arura\Pages\CMS;


use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\Flasher;
use Arura\Form;
use Arura\Settings\Application;
use Arura\User\Logger;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

class ShortUrl{

    protected $token;
    protected $direction;

    protected $isLoaded = false;
    protected $db;

    public function __construct(string $token)
    {
        $this->setToken($token);
        $this->db=new Database();
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
        if (!empty($tag)){
            $analytics = new Analytics(true);
            $analytics
                ->setProtocolVersion('1')
                ->setTrackingId(Application::get("analytics google", "Tag"))
                ->setDocumentPath("/r/{$Url->getToken()}")
                ->setIpOverride($_SERVER["REMOTE_ADDR"])
                ->sendPageview();
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
        $token = getHash("tblShortUrl", "Url_Token", 10);
        $result = $db->createRecord("tblShortUrl", [
            "Url_Token" => $token,
            "Url_Direction" => $$destination
        ]);
        if ($result){
            return new self($token);
        }
        return false;
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
        $form->addText("Url_Direction");

        if (!is_null($shortUrl)){
            $form->setDefaults($shortUrl->__ToArray());
        }

        $form->addSubmit("submit", "Opslaan");
        if ($form->isSubmitted()){

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
                    Flasher::addFlash("Profiel opgeslagen");
                }
            }
        }
    }


}