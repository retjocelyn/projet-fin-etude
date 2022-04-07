<?php 
require_once './view/UserView.php';
require_once './view/ProductView.php';
require_once './Repository/UserRepository.php';
require_once './model/class/Product.php';
require_once './model/class/Product.php';
require_once './Repository/ProductRepository.php';
require_once './Repository/CategoryRepository.php';
require_once './Repository/OrderRepository.php';
require_once './model/class/User.php';
require_once './model/class/Order.php';

class AdminController {
    
    /*private $view; interet???*/
    
    
     public function __construct()
    {
        $this->view = new UserView();
        $this->productView = new ProductView();
        $this->repository = new UserRepository();
        $this->basket = new BasketRepository();
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->orderRepository = new OrderRepository();
    }
    
    public function  adminAccount()
    {
        
        if(!isset($_SESSION['user']) or $_SESSION['role'] !== "admin"){
            header('location: ./index.php?url=login');
            exit();
        }
        
        /*$_SESSION['csrf'] = bin2hex(random_bytes(32));*/
        
        $datas = $this->productRepository->findAll();
        
        $products = [];
        
        foreach($datas as $data){
            $product = new Product();
            $product->setId($data['id']);
            $product->setName($data['name']);
            $product->setQuantity($data['quantity']);
            $product->setPrice($data['price']);
            $product->setImage($data['url_picture']);
            $product->setDescription($data['description']);
            $product->setCategory($data['category_id']);
            
            
            $products[] = $product;
           
        }
        
        $datas = $this->categoryRepository-> findAll();
        
        $categories = [];
        
        foreach($datas as $data){
            $category = new Category();
            $category->setId($data['id']);
            $category->setName($data['name']);
            $category->setUrlImage($data['url_picture']);
            
            $categories[] = $category;
           
        }
         $datas = $this->orderRepository->findAllOrders();
         $commandes = [];
         var_dump($datas);
         die();
        
        foreach($datas as $data){
            $order = new Order();
            $order->setId($data['id']);
            $order->setDate($data['']);
            $order->setCommandUserFamilyName($data['first_name']);
            $order->settCommandUserName($data['last_name']);
            $order->setCommandUserAdress($data['adress']);
            $order->setCommandProductName($data['name']);
            $order->setCommandProductQuantity($data['quantity']);
            $order->setCommandProductPrice($data['price']);
            
             $commandes[] = $order;
         }
        
        
         echo $this->productView->displayadminAccount($products,$categories,$commandes);
    }
    
}






