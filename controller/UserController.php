<?php 
require_once './view/UserView.php';
require_once './repository/UserRepository.php';
require_once './repository/BasketRepository.php';
require_once './repository/OrderRepository.php';
require_once './model/class/User.php';
require_once './service/Authentificator.php';

class UserController {
    
    public function __construct()
    {
       
        $this->view = new UserView();
        $this->repository = new UserRepository();
        $this->basket = new BasketRepository();
        $this->order = new OrderRepository();
        $this->authentificator = new Authentificator();
       
    }
    
    public function login(): void
    {
        
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        
        echo $this->view->displayLogin();
    }
    
    public function loginSecurity():void
    {
        
        if(!isset($_POST['email'], $_POST['password'])){
            
            $_SESSION['error'] = "vous n'avez pas remplis le formulaire";
            header('location:./index.php?url=login');
            exit();
        }
          
      
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        
        $data = $this->repository->fetchLogin($email); 
       
        if(isset($data['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
        
        if($data === false){
            $_SESSION ['error'] = "Identifiant incconu";
            header('location:./index.php?url=login');
            exit();
          }
        
       
        if(!password_verify($password, $data['password'])){
            $_SESSION ['error'] = "Mauvais mot de passe";
            header('location:./index.php?url=login');
            exit();
        }
         
        if($data){
          
            $user = new User();
            $user->setid($data['id']);
            $user->setlastName($data['last_name']);
            $user->setFirstName($data['first_name']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            $user->setRole($data['role']);
            $user->setAdresse($data['adress']);
            $user->setWallet($data['wallet']);
            
              
            $_SESSION['user'] = serialize($user);
          
            if($user->getRole() === "admin"){
                header('location: ./index.php?url=adminAccount');
                exit();
            }else{
                header('location: ./index.php?url=account');
                exit();
            }
            
        }
    }
    
    public function account()
    {
         
        $userAuth = $this->authentificator->checkUser();
       
        if($userAuth->getRole() === "admin"){
            header('location: ./index.php?url=adminAccount');
            exit();
        }
      
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
       
       /* utilisation du CRUD plutot que $userauth pour montrer que je sais recuprer de la data*/
        $data = $this->repository->findById($userAuth->getId());
        
        if(isset($data['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
        
        $user = new User();
        $user->setId($data['id']);
        $user->setlastName($data['last_name']);
        $user->setFirstName($data['first_name']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setRole($data['role']);
        $user->setAdresse($data['adress']);
        $user->setWallet($data['wallet']);
      
        echo $this->view->displayAccount($user);
    }
    
    public function register()
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        echo $this->view->displayRegister();
    }

    public function registerSecurity() : void
    {
    
        if(empty($_POST)){
            
            $_SESSION['error'] = "document non remplis";
            header('location: ./index.php?url=register');
            exit();
        }
        
        $this->authentificator->csrfTokenChecker();
        
        $email = htmlspecialchars($_POST['email']);
        
        $data = $this->repository->fetchLogin($email);
            
        if(isset($data['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
        
        if($data !== false){    
            $_SESSION['error'] = "Email existe d??ja";
            header('location:./index.php?url=register');
            exit();
            
        }
        
        if($_POST['password'] !== $_POST['checkPassword']){
            $_SESSION['error'] = "Le mot de passe n'est pas le m??me";
            header('location:./index.php?url=register');
            exit();
        }
        
        if(isset($_POST['lastName'],$_POST['firstName'],$_POST['email'], $_POST['password'],$_POST['checkPassword'],$_POST['adress'])){
            $user = new User();
            $user->setlastName(htmlspecialchars($_POST['lastName']));
            $user->setFirstName(htmlspecialchars($_POST['firstName']));
            $user->setEmail(htmlspecialchars($_POST['email']));
            $user->setAdresse(htmlspecialchars($_POST['adress']));
            $Pass = htmlspecialchars($_POST['password']);
            $user->setPassword(password_hash($Pass, PASSWORD_DEFAULT));
            $user->setWallet(0,00);
            $user->setRole('client');
          
          
            if($query = $this->repository->createUser($user)){
              
                header('location: ./index.php?url=registerAccepted&message=votre compte a ??t?? cr??e');
                exit();
            }
         
            header('location: ./index.php?url=confirmationOrNot&message=Compte non cr??e');
            exit();
         }    
    }     
    
    public function registerAccepted()
    {
        if(isset($_GET['message'])){
            $_SESSION['message'] = $_GET['message'];
        }else{
            $_SESSION['message'] = 'erreur';
        }
        
        echo $this->view->displayRegisterAccepted();
    }
    
   
    public function formModifyUser():void
    {
       
        $userAuth = $this->authentificator->checkUser();
        
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        
        $userId = $userAuth->getId();
        
        $data = $this->repository->findById($userId);
        
        if(isset($data['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
        
        $user = new User();
        $user->setId($data['id']);
        $user->setlastName($data['last_name']);
        $user->setFirstName($data['first_name']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setRole($data['role']);
        $user->setAdresse($data['adress']);
        $user->setWallet($data['wallet']);
       
        echo $this->view->displayFormModifyUser($user);
    }
    
    
    public function modifyUser():void
    {
        $userAuth = $this->authentificator->checkUser();
        $this->authentificator->csrfTokenChecker();
       
        
        if(empty($_POST)){
            
            $_SESSION['error'] = "document non remplis";
            header('location: ./index.php?url=formModifyUser');
            exit();
        }
        
       
        
        if(isset($_POST['lastName'],$_POST['firstName'],$_POST['email'], $_POST['newPassword'],$_POST['checkNewPassword'],
            $_POST['adress'])){
          
            if($_POST['newPassword'] !== $_POST['checkNewPassword']){
                $_SESSION['error'] = "Le mot de passe n'est pas le m??me";
                header('location:./index.php?url=formModifyUser');
                exit();
            }
            
            $user = new User();
            $user->setId($_POST['id']);
            $user->setlastName(htmlspecialchars($_POST['lastName']));
            $user->setFirstName(htmlspecialchars($_POST['firstName']));
            $user->setEmail(htmlspecialchars($_POST['email']));
            $user->setAdresse(htmlspecialchars($_POST['adress']));
            $user->setRole(htmlspecialchars($_POST['role']));
            $user->setWallet(htmlspecialchars((float)$_POST['userwallet']));
            $Pass = htmlspecialchars($_POST['newPassword']);
            $user->setPassword(password_hash($Pass, PASSWORD_DEFAULT));
            
            $data = $this->repository->fetchLogin($user->getEmail());
            
            if(isset($data['error'])){
                header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
                exit();
            }
            
            if($user->getEmail() !== $userAuth->getEmail() && $data !== false){    
                $_SESSION['error'] = "Email deja utilis??";
                header('location:./index.php?url=formModifyUser');
                exit();
                
            }
        
            if($this->repository->modifyUser($user)){
           
                $_SESSION['user'] = serialize($user);
                
                header('location: ./index.php?url=confirmationOrNot&message="Compte modifi??"');
                exit();
            }   
        }
        header('location: ./index.php?url=confirmationOrNot&message="Compte non modifi??"');
        exit();
        
    }
    
    
    public function addMoney() : void
    {
        
        $userAuth = $this->authentificator->checkUser();
        $this->authentificator->csrfTokenChecker();
        
        if(!isset($_POST['amount'])){
            header('location: ./index.php?url=confirmationOrNot&message="Argent non ajout??"');
            exit();
        }    
           
            $amount = (float)htmlspecialchars($_POST['amount']);
            
            $datas = $this->repository->findById($userAuth->getId());
            
            if(isset($datas['error'])){
                header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
                exit();
            }
            
            $userWallet = (float)$datas['wallet'];
            
            $newAmount = $userWallet + $amount;
           
            if($this->repository->addMoney($userAuth->getId(),$newAmount)){
                
                $userAuth->setWallet($newAmount);
                $_SESSION['user'] = serialize($userAuth);
               
                header('location: ./index.php?url=confirmationOrNot&message="Argent ajout??"');
                exit();
            
            }
        
            header('location: ./index.php?url=confirmationOrNot&message="Argent non ajout??"');
            exit();
    }
    
     
     
    public function logout() : void
    {
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
        
        session_destroy();
        header('location: ./index.php?url=home');
        exit();
    }
    
    public function deleteUser() : void
    {
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
       
        if($this->repository->deleteUser($userAuth->getId())){
        
            session_destroy();
            
            header('location: ./index.php?url=confirmationOrNot&message=votre compte a ??t?? effac??');
            exit();
        }
        
        header('location: ./index.php?url=confirmationOrNot&message=compte non effac??');
        exit();
    }
    
    
    public function basket() : void
    {
        /*verifie si user est connect??*/
        $userAuth = $this->authentificator->checkUser();
        
        /*creer la valeur du token de s??curit?? de la page*/
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        
        /*va chercher les information li??es au panier du user*/
        $datas = $this->basket->findById($userAuth->getId());
        
        /*si erreur base de donn??e ou table*/
        if(isset($datas['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
        
        /*si user a un panier vide*/
        if(empty($datas)){
           echo $this->view->displayEmptyBasket();
           exit();
        }
        
        $prices = [];
        $products = [];
            
        /*pour chaque produit dans panier du user */    
        foreach($datas as $data){
            $product = new Product();
            $product->setInsideBasketId($data['id']);
            $product->setName($data['name']);
            $product->setQuantity($data['quantity']);
            $product->setPrice($data['price']);
            $product->setImage($data['url_picture']);
            $product->setDescription($data['description']);
            $product->setCategory($data['category_id']);
            
            /* ensemble des produit dans panier choisis par le user*/
            $products[] = $product;
            /*r??cupere le prix de chaque article*/
            $prices[] = $product->getPrice();
        }
       /*recup??re le total de la somme du panier*/
        $totalPrice = array_sum($prices);
        
        /*argent qui reste au user ap??s la transaction*/
        $amountAfterBuy = ($userAuth->getWallet()-$totalPrice);
        
        /*affiche panier du user et le produits choisis*/
        echo $this->view->displayBasket($products,$totalPrice,$userAuth,$amountAfterBuy);
    }
   
    public function addArticleToBasket()
    {
      
        $userAuth = $this->authentificator->checkUser();
        $this->authentificator->csrfTokenChecker();
            
        $productId = $_POST['id'];
            
        if($this->basket->addArticleToBasket($userAuth->getId(),$productId)){
        
            header('location: ./index.php?url=confirmationOrNot&message=article ajout?? au panier');
            exit();
        }    
       
        header('location: ./index.php?url=confirmationOrNot&message=article non ajout?? au panier');
        exit();
    }
    
    
    public function deleteArticleFromBasket()
    {
        
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
        
        if(!isset($_POST['id'])){
            
            header('location: ./index.php?url=shop');
            exit();
            
        }
        
        $ArticleBasketId = $_POST['id'];
       
       
        if($this->basket->deleteArticleFromBasket($ArticleBasketId)){
        
            header('location: ./index.php?url=confirmationOrNot&message=article supprim?? du panier');
            exit();
        
        }
        
        header('location: ./index.php?url=confirmationOrNot&message=article non supprim?? du panier');
        exit();
    }
    
    public function deleteBasket() : void
    {
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
        
       
       if($this->basket->deleteBasket($userAuth->getId())){
    
            header('location: ./index.php?url=confirmationOrNot&message=panier supprim??');
            exit();
        }
    
        header('location: ./index.php?url=confirmationOrNot&message=panier non supprim??');
        exit();
        
    }
    
    public function showOrders() : void
    {
        
        $userAuth = $this->authentificator->checkUser();
       
        $datas = $this->order->findById($userAuth->getId());
        
        if(isset($datas['error'])){
            header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
            exit();
        }
      
        if(empty($datas)){
           echo $this->view->displayEmptyOrders();
           exit();
        }
        
        $orders = [];
           
        foreach($datas as $data){
            $order = new Order();
            $order->setCommandProductId($data['product_id']);
            $order->setCommandProductName($data['name']);
            $order->setCommandProductQuantity($data['quantity']);
            $order->setCommandProductPrice($data['price']);
            $order->setCommandProductImage($data['url_picture']);
            $order->setCommandProductDescription($data['description']);
            $order->setStatus($data['order_status']);
            
            $orders[] = $order;
           
        }
       
        echo $this->view->displayOrder($orders);
   }
    
    public function createOrder(): void 
    {
        
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
       
       if(!isset($_POST['amount_after_buy']) || $_POST['amount_after_buy']<0){
            header('location: ./index.php?url=confirmationOrNot&message= Fonds insufisants');
            exit();
       }
       
        $userAuth->setWallet($_POST['amount_after_buy']);
        
       
        if(!$this->repository->addMoney($userAuth->getId(),$userAuth->getWallet())){
            header('location: ./index.php?url=confirmationOrNot&message= Commande non cr????e');
            exit();
        }
        
        $_SESSION['user'] = serialize($userAuth);
        
        if($datas = $this->basket->findById($userAuth->getId())){
            
            if(isset($datas['error'])){
                header('location:./index.php?url=confirmationOrNot&message=Une erreur est survenue');
                exit();
            }
         
            foreach($datas as $data){
                 
                $this->order->createOrder($userAuth->getId(),$data['product_id']);
    
                $this->basket->deleteBasket($userAuth->getId());
            }
          
         
           header('location: ./index.php?url=confirmationOrNot&message=Votre commande ?? ??t?? cr????e');
           exit();
       }   
       
       header('location: ./index.php?url=confirmationOrNot&message=Commande non cr????e');
       exit();
    }
    
    
    public function deleteOrder(): void
    {
        $this->authentificator->csrfTokenChecker();
        $userAuth = $this->authentificator->checkUser();
       
        
       if($this->order->deleteOrder($userAuth->getId())){
        
            header('location: ./index.php?url=confirmationOrNot&message=commande supprim??e');
            exit();
            
       }
        header('location: ./index.php?url=confirmationOrNot&message=commande non supprim??e');
        exit();
   }
}






