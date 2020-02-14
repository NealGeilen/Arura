<?php
namespace Arura\User;

use Arura\Exceptions\Error;
use Arura\Modal;
use Arura\Database;
use Arura\Exceptions\NotAcceptable;
use Arura\Mailer\Mailer;
use Arura\Settings\Application;
use PHPMailer\PHPMailer\Exception;
use SmartyException;

class Recovery extends Modal {
    protected $iTimeOfCreation;
    protected $oUser;
    protected $sToken;

    public function __construct($sToken)
    {
        parent::__construct();
        $this->sToken = $sToken;
    }

    /**
     * @param bool $force
     * @throws Error
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            $aRecord = $this->db->fetchRow("SELECT * FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token"=>$this->getToken()]);
            $this->setUser(new User($aRecord["PR_User_Id"]));
            $this->setTimeOfCreation($aRecord["PR_Time"]);
            $this -> isLoaded = true;
        }
    }

    /**
     * @return bool
     * @throws Error
     */
    public function hasUserToken(){
        $aUser = $this->db->fetchAll("SELECT PR_Token FROM tblPasswordRecovery WHERE PR_User_Id = :Id", ["Id"=>$this->getUser()->getId()]);
        return (count($aUser) > 0);
    }

    /**
     * @return bool
     * @throws Error
     */
    public  function removeTokenFromUser(){
        $this->db->query("DELETE FROM tblPasswordRecovery WHERE PR_User_Id = :Id", ["Id" => $this->getUser()->getId()]);
        return $this->db->isQuerySuccessful();
    }


    /**
     * @param $sToken
     * @return bool
     * @throws Error
     */
    public static function isTokenValid($sToken){
        $db = new Database();
        return (count($db -> fetchAll("SELECT * FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token" => $sToken])) > 0);
    }

    /**
     * @param User $oUser
     * @return Recovery
     * @throws Error
     */
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

    /**
     * @return bool
     * @throws Error
     */
    private function removeRecovery(){
        $this->db->query("DELETE FROM tblPasswordRecovery WHERE PR_Token = :Token", ["Token" => $this->getToken()]);
        return $this->db->isQuerySuccessful();
    }

    /**
     * @return bool
     * @throws Error
     * @throws Exception
     * @throws SmartyException
     */
    public function sendRecoveryMail(){
        $oMailer = new Mailer();
        $oMailer->isHTML(true);
        Mailer::getSmarty()->assign("TOKEN", $this->getToken());
        Mailer::getSmarty()->assign("aUser", $this->getUser()->__toArray());
        $oMailer->setBody(__RESOURCES__ . "Mails" . DIRECTORY_SEPARATOR . Application::get('plg.recovery','template'));
        $oMailer->setSubject("Wachtwoord herstel");
        $oMailer->addBCC($this->getUser()->getEmail());
        return $oMailer->send();
    }

    /**
     * @param $sPassword
     * @param $sToken
     * @return bool
     * @throws Error
     * @throws NotAcceptable
     */
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