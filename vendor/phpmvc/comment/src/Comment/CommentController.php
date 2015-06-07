<?php 

namespace Phpmvc\Comment; 

/** 
 * To attach comments-flow to a page or some content. 
 * 
 */ 
class CommentController implements \Anax\DI\IInjectionAware 
{ 
    use \Anax\DI\TInjectable; 



    /** 
     * View all comments. 
     * 
     * @return void 
     */ 
    public function viewAction($pageKey = null) 
    { 
     
        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 
         
        $all = $comments->findAll($pageKey); 
         
         
        $this->views->add('comment/comments', [ 
            'comments' => $all, 
        ]); 

    } 



    /** 
     * Add a comment. 
     * 
     * @return void 
     */ 
    public function addAction() 
    { 
        $isPosted = $this->request->getPost('doCreate'); 
         
        if (!$isPosted) { 
            $this->response->redirect($this->request->getPost('redirect')); 
        } 

        $comment = [ 
            'content'   => $this->request->getPost('content'), 
            'name'      => $this->request->getPost('name'), 
            'web'       => $this->request->getPost('web'), 
            'mail'      => $this->request->getPost('mail'), 
            'timestamp' => date("Y/m/d h:i"), 
            'ip'        => $this->request->getServer('REMOTE_ADDR'), 
        ]; 

        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 
        $comments->add($comment, $this->request->getPost('key')); 

        $this->response->redirect($this->request->getPost('redirect')); 
    } 


    public function editAction($id) 
    { 

     $isPosted = $this->request->getPost('doEdit'); 
         
        if (!$isPosted) { 
            $this->response->redirect($this->request->getPost('redirect')); 
        } 

        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 

        $comment = $comments->find($id, $this->request->getPost('key')); 
        $this->views->add('comment/edit', [ 
        'mail'      => $comment['mail'], 
        'web'       => $comment['web'], 
        'name'      => $comment['name'], 
        'content'   => $comment['content'], 
        'id'        => $id, 
        'key'       => $this->request->getPost('key'), 
        'redirect'  => $this->request->getPost('redirect'), 
        ]); 
         
         
         

    } 

    public function saveCommentAction($id) 
    { 
     $isPosted = $this->request->getPost('saveEdit'); 
      
        if (!$isPosted) { 
            $this->response->redirect($this->request->getPost('redirect')); 
        } 

        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 
         
        $comment = array( 
        'mail'      => $this->request->getPost('mail'), 
        'web'       => $this->request->getPost('web'), 
        'name'      => $this->request->getPost('name'), 
        'content'   => $this->request->getPost('content'), 
        'id'        => $id,); 
         
        $comments->edit($id, $comment, $this->request->getPost('key')); 

        $this->response->redirect($this->request->getPost('redirect')); 
         

    } 
     
    public function deleteAction($id) 
    { 
     $isPosted = $this->request->getPost('doDelete'); 
      
        if (!$isPosted) { 
            $this->response->redirect($this->request->getPost('redirect')); 
        } 

        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 
        $comments->deleteSingle($id, $this->request->getPost('key')); 
         
        $this->response->redirect($this->request->getPost('redirect')); 
         

    } 
     
     
     
     
    /** 
     * Remove all comments. 
     * 
     * @return void 
     */ 
    public function removeAllAction() 
    { 
        $isPosted = $this->request->getPost('doRemoveAll'); 
         
        if (!$isPosted) { 
            $this->response->redirect($this->request->getPost('redirect')); 
        } 

        $comments = new \Phpmvc\Comment\CommentsInSession(); 
        $comments->setDI($this->di); 

        $comments->deleteAll($this->request->getPost('key')); 

        $this->response->redirect($this->request->getPost('redirect')); 
    } 
}  