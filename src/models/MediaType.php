<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class MediaType {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all media types
    public function getAll() {
        $sql = "SELECT MediaTypeId, Name FROM MediaType ORDER BY Name";
        return $this->db->fetchAll($sql);
    }
    
    // Get media type by ID
    public function getById($id) {
        $sql = "SELECT MediaTypeId, Name FROM MediaType WHERE MediaTypeId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
} 