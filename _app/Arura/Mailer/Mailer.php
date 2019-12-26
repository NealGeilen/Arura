<?php

namespace Arura\Mailer;

use Arura\Exceptions\Error;
use Arura\Settings\Application;
use Html2Text\Html2Text;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends PHPMailer{

    protected static $smarty = null;

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

    public function setBody($sFile, $includeAltBody = true){
        if (is_file($sFile)){
            self::getSmarty()->assign("aWebsite", Application::getAll()["website"]);
            $this->Body = self::getSmarty()->fetch($sFile);
            if ($includeAltBody) {
                $html2text = new Html2Text($this->Body);
                $this->AltBody = $html2text->getText();
            }
        } else {
            throw new Error();
        }
    }

    public function send(){
        if (!parent::send()){
            throw new Error();
            return false;
        }
        return true;
    }

    public static function getSmarty() : \Smarty{
        if (is_null(self::$smarty)){
            self::$smarty = new \Smarty();
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