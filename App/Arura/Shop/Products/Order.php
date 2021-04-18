<?php
namespace Arura\Shop\Products;

use Arura\Database;

class Order{

    protected $db;
    protected $isLoaded;


    protected $id;
    protected $date;
    protected $customer;
    protected $paymentStatus;

    public function __construct($iOrderId)
    {
        $this->setId($iOrderId);
        $this->db = new Database();
    }

    /**
     * Set values on properties
     * @param bool $force
     */
    public function load($force = false){
        if (!$this->isLoaded || $force) {
            //load user properties from database
            $aOrder = $this -> db -> fetchRow("SELECT * FROM tblOrders WHERE Order_Id = ? ", [$this -> getId()]);
            $this->setCustomer(new Customer($aOrder["Order_Customer_Id"]));
            $this->setDate($aOrder["Order_Date"]);
            $this -> isLoaded = true;
        }
    }

    /**
     * Save the properties to the database
     */
    public function save(){
        if ($this->isLoaded){
            $this->db->updateRecord("tblOrders",$this->__toArray(), "Order_Id");
            return $this -> db -> isQuerySuccessful();
        } else {
            return false;
        }
    }

    /**
     * Set user properties to array
     * @return array
     */
    public function __toArray(){
        $this->load();
        $a = [
            "Order_Id" => $this->id,
            "Order_Customer_Id" => $this->customer->getId(),
            "Order_Date" => $this->date,
        ];
        return $a;
    }

    public static function createOrder(Customer $oCustomer){
        $db = new Database();
        $i = $db -> createRecord("tblOrders",[
           "Order_Customer_Id" => $oCustomer->getId()
        ]);
        return new self($i);
    }


    private function hasOrderProduct(Product $oProduct) : bool{
        $aData = $this->db->fetchAll("SELECT OrderRow_Order_Id FROM tblOrderRow WHERE OrderRow_Order_Id = :Order_Id AND OrderRow_Product_Id = :Product_Id",[
           "Order_Id" => $this->getId(),
           "Product_Id" => $oProduct->getId()
        ]);
        return (count($aData) > 0);
    }

    public function getProductFromOrder(Product $oProduct){
        $aProduct = $this->db->fetchRow("SELECT * FROM tblOrderRow JOIN tblProducts ON OrderRow_Product_Id = Product_Id WHERE OrderRow_Order_Id = :Order_Id AND OrderRow_Product_Id = :Product_Id", [
            "Order_Id" => $this->getId(),
            "Product_Id" => $oProduct->getId()
        ]);
        return $aProduct;
    }

    public function getProducts() : array {
        $aProducts = $this->db->fetchAll("SELECT * FROM tblOrderRow JOIN tblProducts ON OrderRow_Product_Id = Product_Id WHERE OrderRow_Order_Id = :Order_Id", [
           "Order_Id" => $this->getId()
        ]);
        return $aProducts;
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
    public function getDate()
    {
        $this->load();
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getCustomer() : Customer
    {
        $this->load();
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }
}