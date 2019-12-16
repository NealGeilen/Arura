<?php

namespace Arura\Shop;

class Cart{

    const SHOPPING_COOKIE = "Shopper";


    public static function setCart(){
        setcookie(self::SHOPPING_COOKIE, []);
    }

    public static function getCart(){
        if (!isset($_COOKIE[self::SHOPPING_COOKIE])){
            self::setCart();
        }
        return $_COOKIE[self::SHOPPING_COOKIE];
    }

    public static function isProductInCart(ProductEnum $oProduct){
        return isset(self::getCart()[$oProduct->getId()]);
    }

    public static function addProduct(ProductEnum $oProduct, $iAmount = 0){
        self::getCart()[$oProduct->getId()]["Details"] = $oProduct->__toArray();
        self::getCart()[$oProduct->getId()]["Amount"] +=  (int)$iAmount;
    }

    public static function removeProduct(ProductEnum $oProduct, $iAmount = 0){
        if ($iAmount === self::getCart()[$oProduct->getId()]){
            unset(self::getCart()[$oProduct->getId()]);
        } else{
            self::getCart()[$oProduct->getId()]["Amount"] -=  (int)$iAmount;
        }
    }

    public static function OrderCart(){

    }


}