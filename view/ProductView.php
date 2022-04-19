<?php 

require_once './model/ProductPage.php';


class ProductView {
    
    /**
     * @return string
     */ 
    
    
    public function dislplayInstruments($products): string
    {
        $page = new ProductPage();
        $page->setProducts($products);
        $page->constructProducts();
        return $page->getPage();
    }
    
    public function displayadminAccount($products,$categories,$commandes,$users)
    {
        $page = new ProductPage();
        $page->setProducts($products);
        $page->setCategories($categories);
        $page->setOrders($commandes);
         $page->setUsers($users);
        $page->adminAccountPage();
        return $page->getPage();
    }
    
    public function displayFormModifyProduct($product,$categories)
    {
        $page = new ProductPage();
        $page->setCategories($categories);
        $page->CreateFormModifyProduct($product);
        return $page->getPage();   
    }
    
     public function displayFormModifyCategory($category)
    {
        $page = new ProductPage();
        $page->CreateFormModifyCategory($category);
        return $page->getPage();   
    }
    public function displayOneProduct($product)
    {
        $page = new ProductPage();
        $page->displayOneProduct($product);
        return $page->getPage();  
    }
    
    public function displayBasket($products,$totalprice)
    {
        $page = new ProductPage();
        $page->setProducts($products);
        $page->setTotalPrice($totalprice);
        $page->basketPage();
        return $page->getPage();   
    }
    public function displayEmptyBasket()
    {
        $page = new ProductPage();
        $page->emptyBasketPage();
        return $page->getPage();   
    }
    
    public function displayOrder($products)
    {
        $page = new ProductPage();
        $page->setProducts($products);
        $page->orderPage();
        return $page->getPage();   
    }
    
     public function displayEmptyOrders()
    {
        $page = new ProductPage();
        $page->emptyOrdersPage();
        return $page->getPage();   
    }
    
}