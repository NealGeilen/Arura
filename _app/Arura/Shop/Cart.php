<?php

namespace Arura\Shop;

use Arura\Shop\Products\ProductEnum;

class Cart{

    const SHOPPING_COOKIE = "Shopper";

    public static function isCartSet(){
        return isset($_COOKIE[self::SHOPPING_COOKIE]);
    }

    public static function isCartContents(){
        if (self::isCartSet()){
            return !empty(self::getCart());
        }
        return false;
    }

    public static function setCart(){
        setcookie(self::SHOPPING_COOKIE, []);
    }

    public static function getCart(){
        if (!self::isCartSet()){
            self::setCart();
        }
        return $_COOKIE[self::SHOPPING_COOKIE];
    }

    public static function isProductInCart(ProductEnum $oProduct){
        return isset($_COOKIE[self::SHOPPING_COOKIE][get_class($oProduct)][$oProduct->getId()]);
    }

    public static function addProduct(ProductEnum $oProduct, $iAmount = 0){
        if (self::isProductInCart($oProduct)){
            $_COOKIE[self::SHOPPING_COOKIE][get_class($oProduct)][$oProduct->getId()]["Details"] = $oProduct->__toArray();
        }
        $_COOKIE[self::SHOPPING_COOKIE][get_class($oProduct)][$oProduct->getId()]["Amount"] +=  (int)$iAmount;
    }

    public static function removeProduct(ProductEnum $oProduct, $iAmount = 0){
        if ((int)$iAmount === (int)self::getCart()[get_class($oProduct)][$oProduct->getId()]["Amount"]){
            unset($_COOKIE[self::SHOPPING_COOKIE][get_class($oProduct)][$oProduct->getId()]);
        } else{
            $_COOKIE[self::SHOPPING_COOKIE][get_class($oProduct)][$oProduct->getId()]["Amount"] -=  (int)$iAmount;
        }
    }


}