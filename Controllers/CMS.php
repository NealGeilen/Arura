<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Client\Request;
use Arura\Client\RequestHandler;
use Arura\Client\ResponseHandler;
use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Flasher;
use Arura\Form;
use Arura\Pages\CMS\ContentBlock;
use Arura\Pages\CMS\Group;
use Arura\Pages\CMS\Sitemap;
use Arura\Pages\Page;
use Arura\Permissions\Right;
use Arura\Router;
use Arura\User\Logger;

class CMS extends AbstractController {

    /**
     * @Route("/content/paginas")
     * @Right("CMS_PAGES")
     */
    public function Pages(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("delete-page", function ($aData){
                $p = new \Arura\Pages\CMS\Page($aData["Page_Id"]);
                return $p->delete();
            });
        });
        $form = \Arura\Pages\CMS\Page::getForm();
        if ($form->isSuccess()){
            \Arura\Pages\CMS\Page::Create($form->getValues('array'));
            Flasher::addFlash("Pagina aangemaakt");
        }
        $this->render("AdminLTE/Pages/CMS/Pages.tpl", [
            "title" =>"Pagina's",
            "createForm" => (string)$form,
            "createFormError" =>$form->hasErrors(),
            "Pages" => \Arura\Pages\CMS\Page::getAllPages()
        ]);
    }

    /**
     * @Route("/content/menu")
     * @Right("CMS_MENU")
     */
    public function Menu(){
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler){
            $requestHandler->addType("get", function (){
                return (json_array_decode(file_get_contents(__WEB_TEMPLATES__.'menu.json')));
            });
            $requestHandler->addType("set", function ($aData){
                if (!isset($aData['NavData'])){
                    $aNavData = [];
                } else {
                    $aNavData = $aData['NavData'];
                }
                file_put_contents(__WEB_TEMPLATES__.'menu.json', json_encode($aNavData));
            });
            $requestHandler->addType("build-sitemap", function (){
                $Sitemap = new Sitemap();
                $Sitemap->build();
                $Sitemap->save();
            });
            $requestHandler->addType("send-sitemap", function (){
                $Sitemap = new Sitemap();
                $Sitemap->submit();
            });
        });
        Router::addSourceScriptJs(__ARURA__ROOT__ . "/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.js");
        Router::addSourceScriptCss(__ARURA__ROOT__ ."/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.css");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Menu.js");
        $this->render("AdminLTE/Pages/CMS/Menu.tpl", [
            "title" =>"Menu"
        ]);
    }

    /**
     * @Route("/content/pagina/([^/]+)/content")
     * @Right("CMS_PAGES")
     */
    public function Content($id){
        $p = new \Arura\Pages\CMS\Page($id);
        Request::handleXmlHttpRequest(function (RequestHandler $requestHandler, ResponseHandler $responseHandler) use ($p){
            $requestHandler->addType("Page-Content-Structure", function ($aData) use ($p){
                return $p->getPageStructure();
            });
            $requestHandler->addType("Save-Page-Content", function ($aData) use ($p){
                Logger::Create(Logger::UPDATE, \Arura\Pages\CMS\Page::class, $p->getTitle());
                return $p->SavePageContents($aData["Data"]);
            });
            $requestHandler->addType("Create-Block", function ($aData) use ($p){
                return ["Content_Id" => ContentBlock::Create()->getId()];
            });
            $requestHandler->addType("Create-Group", function ($aData) use ($p){
                return Group::Create($p->getId())->get();
            });
        });
        Logger::Create(Logger::READ, \Arura\Pages\CMS\Page::class, $p->getTitle());
        Router::getSmarty() -> assign('aCmsPage', $p->__toArray());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.js");
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.css");
        $this->render("AdminLTE/Pages/CMS/Content.tpl", [
            "title" =>"Pagina content",
            "sPageSideBar" => Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.Sidebar.tpl")
        ]);
    }

    /**
     * @Route("/content/pagina/([^/]+)/instellingen")
     * @Right("CMS_PAGES")
     */
    public function Settings($id){
        $p = new \Arura\Pages\CMS\Page($id);
        $form = \Arura\Pages\CMS\Page::getForm();
        $form->addHidden("Page_Id")
            ->addRule(Form::INTEGER);
        $form->setDefaults($p->__toArray());
        if ($form->isSubmitted()){
            Logger::Create(Logger::UPDATE, \Arura\Pages\CMS\Page::class, $p->getTitle());
            $p->set($form->getValues('array'));
            $p->load(true);
            Flasher::addFlash("Pagina {$p->getTitle()} opgeslagen");
        }
        $this->render("AdminLTE/Pages/CMS/Settings.tpl", [
            "title" =>"Pagina instellingen",
            "form" => (string) $form,
            "aCmsPage" => $p->__toArray(),
            "CmsPage" => $p
        ]);
    }

}