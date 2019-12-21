<?php


namespace Arura\Shop\Products;

interface ProductEnum
{
    public function __construct($ProductId);


    public function doesProductExist() : bool;

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false);

    /**
     * Save the properties to the database
     */
    public function save();

    public function __toArray();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @param mixed $price
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param mixed $description
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getImg();

    /**
     * @param mixed $img
     */
    public function setImg($img);

}