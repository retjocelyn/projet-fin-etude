<?php

require_once './repository/AbstractRepository.php';

 class BasketRepository extends AbstractRepository
{
    
    private const TABLE = "panier";
    
    public function __construct(){
        parent::__construct(self::TABLE);
    }
    
   
    public function addArticleToBasket($userId,$productId): bool
    {
       
        try {
            $query = $this->connexion->prepare('INSERT INTO `panier`( `user_id`, `product_id`, `created_at`) 
            VALUES (:userId,:productId,NOW())');
            if ($query) {
                $query->bindParam(':userId', $userId);
                $query->bindParam(':productId', $productId);
               
               return $query->execute();
            }    
        } catch (Exception $e) {
            return false;
        }
        
    }
       
    
    public function findById($userId)
    {
         $data = null;
        try {
            $query = $this->connexion->prepare('SELECT * FROM products as p INNER JOIN panier as pa ON p.id = pa.product_id WHERE pa.user_id = :id');
            if ($query) {
                $query->bindParam(':id', $userId);
                $query->execute();
                
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            die($e);
        }
        
        return $data;
    }
    
    public function deleteArticleFromBasket($productId,$userId)
    {
       
        try {
            $query = $this->connexion->prepare('DELETE FROM `panier` 
            WHERE product_id = :productId and user_id = :userId  ');
            
            if ($query) {
                $query->bindParam(':userId', $userId);
                $query->bindParam(':productId', $productId);
               
               return $query->execute();
            }    
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    
    public function deleteBasket($userId)
    {
       
         try {
            $query = $this->connexion->prepare('DELETE FROM `panier` 
            WHERE  user_id = :userId  ');
            
            if ($query) {
                $query->bindParam(':userId', $userId);
               
               return $query->execute();
            }    
        } catch (Exception $e) {
            return false;
        }
        
    }
    
    
}