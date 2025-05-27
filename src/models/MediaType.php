<?php

class MediaType {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all media types
     * 
     * @return array
     */
    public function getAll() {
        $sql = "SELECT MediaTypeId, Name FROM MediaType ORDER BY Name";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get media type by ID
     * 
     * @param int $id Media type ID
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT MediaTypeId, Name FROM MediaType WHERE MediaTypeId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
} 