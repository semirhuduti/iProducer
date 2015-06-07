<?php


namespace Anax\Tags;

class AssignTags extends \Anax\MVC\CDatabaseModel {

	/**
         * Remove a tag connection from the database.
         * @param type $id tag id.
         * @return type
         */
	public function delete($id) {
	    $this->db->delete(
	        $this->getSource(),
	        'idComment = ?'
	    );
	 
	    return $this->db->execute([$id]);
	}

	/**
         * Get tag name for tag id.
         * @param type $id tag id.
         * @return type
         */
	public function getTagName($id) {

	    $this->db->select('name')
            ->from('tags')
            ->where('id = ?')
        ;
	 
	    $this->db->execute([$id]);
	    return $this->db->fetchOne();
	}

	
	/**
         * Save a tag connection to the database.
         * @param type $values tag connection object.
         * @return type
         */
	public function save($values = []) {
	    return $this->create($values);
	}


	/**
         * Find matching id and connection in database.
         * @param type $id tag id.
         * @return type
         */
	public function find($id) {
	    $this->db->select('idTag')
	             ->from('assigntags')
	             ->where('idComment = ?');
	 
	    $this->db->execute([$id]);
	    $this->db->setFetchModeClass(__CLASS__);
	    return $this->db->fetchAll();
	}

	

	
	/**
         * Get all tags.
         * @return type
         */
	public function findAllTags() {

	    $this->db->select('id, name')
            ->from('tags')
        ;
	 
	    $this->db->execute();
	    $this->db->setFetchModeClass(__CLASS__);
	    return $this->db->fetchAll();
	}

        /**
         * Get tags that are used.
         * @return type
         */
	public function findTags() {

	    $this->db->select('DISTINCT T.name')
            ->from('tags AS T')
            ->join('assigntags AS C2T', 'T.id = C2T.idTag')
        ;
	 
	    $this->db->execute();
	    $this->db->setFetchModeClass(__CLASS__);
	    return $this->db->fetchAll();
	}


}