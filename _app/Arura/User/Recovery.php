<?php
namespace Arura\User;

use Arura\Exceptions\Error;
use Arura\Form;
use Arura\Modal;
use Arura\Database;
use Arura\Exceptions\NotAcceptable;
use Arura\Mailer\Mailer;
use Arura\Settings\Application;
use Arura\SystemLogger\SystemLogger;
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
        $oRequest->isLoaded = true;
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
        $oMailer->addAddress($this->getUser()->getEmail());
        return $oMailer->send();
    }

    /**
     * @param $sPassword
     * @param $sToken
     * @return bool
     * @throws Error
     * @throws NotAcceptable
     */
    public function setPassword($sPassword){
        if (self::isTokenValid($this->getToken())){
            $this->load();
            $this->getUser()->load();
            $this->getUser()->setPassword(Password::Create($sPassword));
            if ($this->getUser()->save()){
                return $this->removeRecovery();
            }
            return false;
        } else {
            throw new NotAcceptable();
        }
    }

    public static function getRequestForm() :Form
    {
        $form = new Form("request-form", Form::OneColumnRender);
        $form->addEmail("mail", "E-mailadres")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Herstel mail aanvragen");
        if ($form->isSuccess()){
        }
        if ($form->isSuccess()){
            $user = User::getUserOnEmail($form->getValues()->mail);
            if ($user){
                $recovery = self::requestToken($user);
                $recovery->sendRecoveryMail();
            } else {
                SystemLogger::addRecord(SystemLogger::Security, \Monolog\Logger::INFO, "Request for password recovery failed: {$form->getValues()->mail}");
            }
        }
        return  $form;
    }

    public static function getRecoveryForm() : Form{
        $form = new Form("recovery-form");
        $form->addPassword('password', 'Wachtwoord:')
            ->addRule(Form::REQUIRED, "Dit veld is verplicht")
            ->addRule(Form::MIN_LENGTH, 'Je wachtwoord moet minimaal %d karakters lang zijn', 8);
        $form->addPassword('passwordVerify', 'Wachtwoord herhalen:')
            ->addRule(Form::REQUIRED, "Dit veld is verplicht")
            ->addRule(Form::EQUAL, 'Wachtwoord is niet gelijk', $form['password']);
        $form->addSubmit("submit", "Wachtwoord veranderen");
        return  $form;
    }

    /**
     * @return mixed
     */
    public function getTimeOfCreation() : Int
    {
        $this->load();
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
        $this->load();
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