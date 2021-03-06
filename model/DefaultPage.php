<?php 
require_once "./service/Utils.php";
require_once "./service/AbstractPage.php";

class DefaultPage extends AbstractPage {
    
    private string $html;
    
    private string $errors;
    
    private array $data;
    
    private string $csrf;
    
    private string $message;
    
    
    public function __construct(string $html)
    {
        parent::__construct();
        
        $this->html = $html;
        $this->body = $this->utils->searchHtml($html);
        if(!isset($_SESSION['error'])){
            $this->errors = '';
        }
        
        
    }
    
    /**
     * @return string $errors
     */
    public function getErrors(): string
    {
        return $this->errors;
    }
    
     /**
     * @param string $errors
     */
    public function setErrors($errors):void
    {
        $this->errors = $errors;
    }
    
     /**
     * @return string $csrf
     */
    public function getCsrf(): string
    {
        return $this->csrf;
    }
    
     /**
     * @param string $csrf
     */
    public function setCsrf($csrf): void
    {
        $this->csrf = $csrf;
    }
    
     /**
     * @return string $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
     /**
     * @param string $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
    
    
    public function assemblerPage(): void 
    {
        switch($this->html){
            case 'home' : 
            $this->head->setTitle('symphony: page accueil');
            $this->head->setDescription('accueil');
            $this->constructPage();
            break;
                
                
            case'login':
            $this->head->setTitle('symphony: page connexion');
            $this->head->setDescription('formulaire connexion');    
            $this->body = str_replace('{%$token%}',$this->getCsrf(), $this->body);
            $this->body = str_replace('{%error%}',$this->getErrors(), $this->body); 
            $this->constructPage();
            break;
            
            case'register':
               
            $this->head->setTitle('symphony: page inscription');
            $this->head->setDescription('inscription');
            $this->body = str_replace('{%error%}',$this->getErrors(),$this->body);  
            $this->body = str_replace('{%$token%}',$this->getCsrf(), $this->body);    
            $this->constructPage();
            break;
            
            case'registerAccepted':
            $this->head->setTitle('symphony: page inscription');
            $this->head->setDescription('inscription');
            $this->body = str_replace('{%message%}',$this->getMessage(),$this->body);
            $this->constructPage();
            break;
            
            case'confirmationOrNot':
            $this->head->setTitle('symphony: message');
            $this->head->setDescription('message de statut');
            $this->body = str_replace('{%message%}',$this->getErrors(),$this->body);
            $this->constructPage();
            break;
                
        }
        
    }
    
    public function displayAccount(User $user):void
    {
        
            $this->head->setTitle('symphony: page compte');
            $this->head->setDescription('compte');
            $this->body = str_replace('{%nom%}', $user->getLastName(), $this->body);
            $this->body = str_replace('{%prenom%}', $user->getFirstName(), $this->body);
            $this->body = str_replace('{%email%}', $user->getEmail(), $this->body);
            $this->body = str_replace('{%adresse%}', $user->getAdresse(), $this->body);
            $this->body = str_replace('{%solde%}', $user->getWallet(), $this->body);
            $this->body = str_replace('{%id%}', $user->getId(), $this->body);
            $this->body = str_replace('{%$token%}', $_SESSION['csrf'], $this->body);
            $this->constructPage();
        
    }
    
    public function createFormModifyUser(User $user):void
    {
        
        $this->head->setTitle('symphony: modifier profil');
        $this->head->setDescription('formulaire de modification de user');
        $this->body = str_replace('{%error%}',$this->getErrors(),$this->body); 
        $this->body = str_replace('{%familyname%}', $user->getLastName(), $this->body);
        $this->body = str_replace('{%firstName%}', $user->getFirstName(), $this->body);
        $this->body = str_replace('{%email%}', $user->getEmail(), $this->body);
        $this->body = str_replace('{%adress%}', $user->getAdresse(), $this->body);
        $this->body = str_replace('{%id%}', $user->getId(), $this->body);
        $this->body = str_replace('{%role%}', $user->getRole(), $this->body);
        $this->body = str_replace('{%wallet%}', $user->getWallet(), $this->body);
        $this->body = str_replace('{%$token%}', $_SESSION['csrf'], $this->body);
        $this->constructPage();
        
    }
    
    
}