<?php 

namespace Phpmvc\Comment; 

/** 
 * To attach comments-flow to a page or some content. 
 * 
 */ 
class CommentsInSession implements \Anax\DI\IInjectionAware 
{ 
    use \Anax\DI\TInjectable; 
     
    /** 
     * Add a new comment. 
     * 
     * @param array $comment with all details. 
     *  
     * @return void 
     */ 
    public function add($comment, $pageKey) 
    { 
        $comments = $this->session->get('comments', []); 
        $comments[$pageKey][] = $comment; 
        $this->session->set('comments', $comments); 
    } 
     
    public function edit($id, $comment, $pageKey) 
    { 
        $comments = $this->session->get('comments', []); 
        $comments[$pageKey][$id] = $comment; 
        $comments = $this->session->set('comments', $comments); 
         
    } 
     
    /** 
     * Find and return all comments. 
     * 
     * @return array with all comments. 
     */ 
    public function findAll($pageKey) 
    { 
        $comments = $this->session->get('comments', []); 
         
        if($pageKey == null || count($comments)==0 || !array_key_exists ($pageKey , $comments)) 
        { 
            return null; 
        } 
         
        return $comments[$pageKey]; 
    } 
     
    public function find($id, $pageKey) 
     { 
        if($pageKey == null) 
        { 
            return null; 
        } 
         
        $comments =  $this->session->get('comments', []); 
        return $comments[$pageKey][$id]; 
    } 

    public function deleteSingle($id, $pageKey) 
    { 
      $comments = $this->session->get('comments', []); 
      unset($comments[$pageKey][$id]); 
      $this->session->set('comments', $comments); 
    } 

    /** 
     * Delete all comments. 
     * 
     * @return void 
     */ 
    public function deleteAll($pageKey) 
    { 
        $comments = $this->session->get('comments', []); 
        unset($comments[$pageKey]); 
        $this->session->set('comments', $comments); 
    } 
}  
