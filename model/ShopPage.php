<?php 
require_once "./service/Utils.php";
require_once "./service/AbstractPage.php";

class ShopPage extends AbstractPage {
    
    private string $article;
    
    private array $categories;
    
    public function __construct()
    {
        parent::__construct();
       
        $this->body = $this->utils->searchHtml('shop');
        $this->article = '';
        $this->categories = [];
    }
    
    
    /**
     * @return string $article
     */
    public function getArticle(): string
    {
        return $this->article;
    }
    
    /**
     * @param string $article
     */
    public function setArticle(string $article): void
    {
        $this->article = $article;
    }
    
    /**
     * @return array $categories
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
    
    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
    
    
    public function constructShop(): void
    {
        foreach($this->categories as $category){
            $content = $this->utils->searchInc('articleCategory');
            $content = str_replace('{%nom%}', $category->getName(), $content);
            $content = str_replace('{%id%}',$category->getId(), $content);
            $content = str_replace('{%urlImage%}',$category->getUrlImage(), $content);
            
            $this->article .= $content;
        }
        
        $this->body = str_replace('{%article%}', $this->article, $this->body);
        $this->constructPage();
    }
}