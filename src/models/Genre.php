<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class Genre {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all genres
    public function getAll() {
        $sql = "SELECT GenreId, Name FROM Genre ORDER BY Name";
        return $this->db->fetchAll($sql);
    }
    
    // Get genre by ID
    public function getById($id) {
        $sql = "SELECT GenreId, Name FROM Genre WHERE GenreId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
} 