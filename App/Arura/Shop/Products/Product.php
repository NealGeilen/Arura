<?php
namespace Arura\Shop\Products;

use Arura\Database;
use Exception;

class Product implements ProductEnum {

    private $isLoaded;
    private $db;

    protected $id;
    protected $name;
    protected $price;
    protected $description;
    protected $img;


    public function __construct($ProductId)
    {
        $this->id = $ProductId;
        $this->db = new Database();
        if (!$this->doesProductExist()){
            throw new Exception("Product not found", 404);
        }
    }

    public function doesProductExist() : bool{
        $aData = $this->db->fetchAll("SELECT Product_Id FROM tblProducts WHERE Product_Id = :Product_Id",[
            "Product_Id" => $this->getId()
        ]);
        return (count($aData) > 0);
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aProduct = $this -> db -> fetchRow("SELECT * FROM tblProducts WHERE Product_Id = ? ", [$this -> getId()]);
            $this->setDescription($aProduct["Product_Description"]);
            $this->setName($aProduct["Product_Name"]);
            $this->setImg($aProduct["Product_Img"]);
            $this->setPrice($aProduct["Product_Price"]);
            $this -> isLoaded = true;
        }
    }

    /**
     * Save the properties to the database
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblProducts",$this->__toArray(), "Product_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    public function __toArray(){
        $this->load();
        $a = [
            "Product_Id" => $this->id,
            "Product_Name" => $this->name,
            "Product_Price" => $this->price,
            "Product_Description" => $this->description,
            "Product_Img" => $this->img
        ];
        return $a;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        $this->load();
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        $this->load();
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        $this->load();
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        $this->load();
        return $this->img;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img)
    {
        $this->img = $img;
    }

}
