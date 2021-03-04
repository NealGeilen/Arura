<?php
namespace Arura\User;
use Arura\Form;
use Arura\SystemLogger\SystemLogger;

class Password{

    /**
     * @param $pw
     * @return false|string|null
     */
    public static function Create($pw){
        return password_hash($pw, PASSWORD_BCRYPT);
    }

    /**
     * @param $pw
     * @param $hash
     * @return bool
     */
    public static function Verify($pw, $hash){
        return password_verify($pw, $hash);
    }

    public static function loginForm() : Form
    {
        $form = new Form("admin-login", Form::OneColumnRender);
        $form->addEmail("mail", "E-mailadres")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addPassword("password", "Wachtwoord")
            ->addRule(Form::REQUIRED, "Dit veld is verplicht");
        $form->addSubmit("submit", "Inloggen");
        if ($form->isSuccess()) {
            $user = User::getUserOnEmail($form->getValues()->mail);
            if ($user === false) {
                $form->addError("Gegevens onjuist");
            } elseif (!$user->isActive()) {
                $form->addError("Account is gedeactiveerd");
            } else {
                if (Password::Verify($form->getValues()->password, $user->getPassword())) {
                    $user->logInUser();
                } else {
                    User::addLoginAttempt();
                    $form->addError("Gegevens onjuist");
                }
            }

            if ($form->hasErrors()) {
                SystemLogger::addRecord(SystemLogger::Security, \Monolog\Logger::INFO, 'Failed login request: '.$form->getValues()->mail );
            }
        }
        return $form;
    }
}