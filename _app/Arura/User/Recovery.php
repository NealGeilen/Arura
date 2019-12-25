<?php
namespace NG\User;

use NG\Database;
use NG\Exceptions\NotAcceptable;
use NG\Mailer\Mailer;
use NG\Mailer\TemplateHelper;
use NG\Settings\Application;

class Recovery{
    protected $db;

    protected $iTimeOfCreation;
    protected $oUser;
    protected $sToken;
    protected $isLoaded = false;

    public function __construct($sToken)
    {
        $this->sToken = $sToken;
        $this->db = new Database();
    }

    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRecord = $this->db->fetchRow("SELECT * FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token"=>$this->getToken()]);
            $this->setUser(new User($aRecord["PR_User_Id"]));
            $this->setTimeOfCreation($aRecord["PR_Time"]);
            $this -> isLoaded = true;
        }
    }

    public function hasUserToken(){
        $aUser = $this->db->fetchAll("SELECT PR_Token FROM tblPasswordRecovery WHERE PR_User_Id = :Id", ["Id"=>$this->getUser()->getId()]);
        return (count($aUser) > 0);
    }

    public  function removeTokenFromUser(){
        $this->db->query("DELETE FROM tblPasswordRecovery WHERE PR_User_Id = :Id", ["Id" => $this->getUser()->getId()]);
        return $this->db->isQuerySuccessful();
    }


    public static function isTokenValid($sToken){
        $db = new Database();
        return (count($db -> fetchAll("SELECT * FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token" => $sToken])) > 0);
    }

    public static function requestToken(User $oUser){
        $db = new Database();
        $sToken = getHash("tblPasswordRecovery", "PR_Token");
        $oRequest = new self($sToken);
        $oRequest->setTimeOfCreation(time());
        $oRequest->setUser($oUser);
        if ($oRequest->hasUserToken()){
            $oRequest->removeTokenFromUser();
        }
        $oRequest->isLoaded = true;
        $db -> createRecord("tblPasswordRecovery", [
            "PR_User_Id" => $oUser->getId(),
            "PR_Token" => $sToken,
            "PR_Time" => time()
        ]);
        return $oRequest;
    }

    private function removeRecovery(){
        $this->db->query("DELETE FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token" => $this->getToken()]);
        return $this->db->isQuerySuccessful();
    }

    public function sendRecoveryMail(){
        $oMailer = new Mailer();
        $oMailer->isHTML(true);
        $oTemplate = TemplateHelper::fromFile(__RESOURCES__ . "Mails" . DIRECTORY_SEPARATOR . \NG\Settings\Application::get('plg.recovery','template'));
        $oTemplate->replace("USERNAME", $this->getUser()->getUsername());
        $oTemplate->replace("EMAIL", $this->getUser()->getEmail());
        $oTemplate->replace("TOKEN", $this->getToken());
        $oTemplate->replace("URL", Application::get("website", 'url'));
        $oTemplate->replace("TIME", date('m/d/Y H:i:s', $this->getTimeOfCreation()));
        $oMailer->setBodyFromTemplate($oTemplate);
        $oMailer->setSubject("Wachtwoord herstel");
        $oMailer->addBCC($this->getUser()->getEmail());
        return $oMailer->send();
    }

    public function setPassword($sPassword, $sToken){
        if (self::isTokenValid($sToken)){
            $this->load();
            $this->getUser()->load();
            $this->getUser()->setPassword($sPassword);
            if ($this->getUser()->save()){
                return $this->removeRecovery();
            }
            return false;
        } else {
            throw new NotAcceptable();
        }
    }

    /**
     * @return mixed
     */
    public function getTimeOfCreation() : Int
    {
        return $this->iTimeOfCreation;
    }

    /**
     * @param mixed $iTimeOfCreation
     */
    public function setTimeOfCreation($iTimeOfCreation)
    {
        $this->iTimeOfCreation = $iTimeOfCreation;
    }

    /**
     * @return mixed
     */
    public function getUser() : User
    {
        return $this->oUser ;
    }

    /**
     * @param mixed $oUser
     */
    public function setUser(User $oUser)
    {
        $this->oUser = $oUser;
    }

    /**
     * @return mixed
     */
    public function getToken(): String
    {
        return $this->sToken;
    }

}