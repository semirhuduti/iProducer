<?php

namespace Anax\Comment;

/**
 * This is the model used for the comments in disscussions.
 * 
 */
class Comment extends \Anax\MVC\CDatabaseModel {

	
	/**
         * Find a user with a specefied ID.
         * 
         * @param type $id
         * @return type
         */
	public function findByUser($id) {
	    $this->db->select('*')
	             ->from($this->getSource())
	             ->where("userId = ?");
	 
	    $this->db->execute([$id]);
	    return $this->db->fetchAll();
	}

}