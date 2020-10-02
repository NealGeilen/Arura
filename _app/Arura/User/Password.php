<?php
namespace Arura\User;
use Arura\Form;

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
        if ($form->isSubmitted()){
            $user = User::getUserOnEmail($form->getValues()->mail);
            if ($user === false){
                $form->addError("E-mailadres niet beschikbaar");
            } elseif (!$user->isActive()){
                $form->addError("Account is gedeactiveerd");
            }else {
                if(Password::Verify($form->getValues()->password, $user->getPassword())){
                    $user->logInUser();
                } else{
                    User::addLoginAttempt();
                    $form->addError("Wachtwoord onjuist");
                }
            }
        }
        return  $form;
    }
}