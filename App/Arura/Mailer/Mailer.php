<?php

namespace Arura\Mailer;

use Arura\Exceptions\Error;
use Arura\Settings\Application;
use Html2Text\Html2Text;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Smarty;
use SmartyException;

class Mailer extends PHPMailer{

    /**
     * @var null
     */
    protected static $smarty = null;

    /**
     * Mailer constructor.
     * @param bool $setDefaults
     * @throws Error
     * @throws Exception
     */
    public function __construct($setDefaults = true)
    {
        parent::__construct(true);

        if ($setDefaults) {
            $this->isSMTP();
            $this->SMTPDebug  = 0;
            $this->SMTPAuth   = true;
            $this->AuthType   = 'LOGIN';
            $this->Host       = Application::get('smtp', 'server');
            $this->Username   = Application::get('smtp', 'username');
            $this->Password   = Application::get('smtp', 'password');
            $this->SMTPSecure = Application::get('smtp', 'secure');
            $this->Port       = Application::get('smtp', 'port');

            $this->setFrom(Application::get('smtp', 'username'), Application::get("website", "name"));
        }
    }

    /**
     * @param string $sFile
     * @param bool $includeAltBody
     * @throws Error
     * @throws SmartyException
     */
    public function setBody($sFile = "", $includeAltBody = true){
        if (is_file($sFile)){
            self::getSmarty()->assign("aWebsite", Application::getAll()["website"]);
            self::getSmarty()->assign("sContent", self::getSmarty()->fetch($sFile));
            if (is_file(__WEB__ROOT__ . "/_app/Resources/Mails/index.html")){
                $this->Body = self::getSmarty()->fetch(__WEB__ROOT__ . "/_app/Resources/Mails/index.html");
            } else {
                throw new Error("Master template not set");
            }
            if ($includeAltBody) {
                $html2text = new Html2Text($this->Body);
                $this->AltBody = $html2text->getText();
            }
        } else {
            throw new Error("No body file given");
        }
    }

    /**
     * @return bool
     * @throws Error
     * @throws Exception
     */
    public function send(){
        if (!parent::send()){
            throw new Error();
        }
        return true;
    }

    /**
     * @return Smarty
     */
    public static function getSmarty() : Smarty{
        if (is_null(self::$smarty)){
            self::$smarty = new Smarty();
        }
        return self::$smarty;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->Subject;
    }

    /**
     * @param string $Subject
     */
    public function setSubject($Subject)
    {
        $this->Subject = $Subject;
    }


}