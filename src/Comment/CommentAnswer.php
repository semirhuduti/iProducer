<?php



namespace Anax\Comment;

/**
 * This model is used for the comment to comment connection in a disscussion.
 *
 */
class CommentAnswer extends \Anax\MVC\CDatabaseModel {

	/**
         * Used to delecte comment answers to a comment.
         * @param type $id id of the comment that is to be deleted.
         * @return type result of the removal of the comment.
         */
	public function delete($id) {
	    $this->db->delete(
	        $this->getSource(),
	        'idQuestion = ?'
	    );
	 
	    return $this->db->execute([$id]);
	}

	/**
         * Save a comment answer from a valuses vector.
         * @param type $values values of the comment answer that is to be added to the database.
         * @return type result of the addition to the comment in the database.
         */
	public function save($values = []) {
            
	    return $this->create($values);
	}

	/**
         * Used to find a comment answer.
         * @param type $id of the comment.
         * @return type comments that were found in the database.
         */
	public function find($id) {
	    $this->db->select('idAnswer')
	             ->from($this->getSource())
	             ->where('idQuestion = ?');
	 
	    $this->db->execute([$id]);
	    $this->db->setFetchModeClass(__CLASS__);
	    return $this->db->fetchAll();
	}

	/**
         * Used to check if a comment is a comment answer.
         * @param type $id of the comment that is to be checked.
         * @return boolean outcome of the check.
         */
	public function isAnswer($id) {
	    $this->db->select('*')
	             ->from($this->getSource())
	             ->where('idAnswer = ?')
	    ;
	 
	    $this->db->execute([$id]);
	    $this->db->setFetchModeClass(__CLASS__);
	    $res = $this->db->fetchAll();

    	if ($res != null) { 
    		return true; 
    	} else { 
    		return false;
    	}
	}

	/**
         * Get the comment answers to a comment.
         * @param type $id of the comment.
         * @return type vector with all comments connected to a comment.
         */
	public function numberAnswers($id) {
            
	    $this->db->select('idQuestion, count(*) AS answers')
	             ->from($this->getSource())
	             ->where('idQuestion = ?')
	    ;
	 
	    $this->db->execute([$id]);
	    $this->db->setFetchModeClass(__CLASS__);
	    return $this->db->fetchAll();
	}


}