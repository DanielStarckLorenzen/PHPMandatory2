<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class Artist {
    private $db;
    
    public function __construct($database = null) {
        $this->db = $database ?? Database::getInstance();
    }
    
    // Get all artists
    public function getAll($search = null) {
        $sql = "SELECT ArtistId, Name FROM Artist";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE Name LIKE ?";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY Name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get artist by ID
    public function getById($id) {
        $sql = "SELECT ArtistId, Name FROM Artist WHERE ArtistId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    // Get albums by artist ID
    public function getAlbums($id) {
        $sql = "SELECT AlbumId, Title FROM Album WHERE ArtistId = ? ORDER BY Title";
        return $this->db->fetchAll($sql, [$id]);
    }
    
    // Create a new artist
    public function create($name) {
        $sql = "SELECT MAX(ArtistId) as maxId FROM Artist";
        $result = $this->db->fetchOne($sql);
        $maxId = $result['maxId'];
        $newId = $maxId + 1;
        $sql = "INSERT INTO Artist (ArtistId, Name) VALUES (?, ?)";
        $this->db->query($sql, [$newId, $name]);
        return $newId;
    }
    
    // Delete an artist
    public function delete($id) {
        // Check if artist has albums
        $sql = "SELECT COUNT(*) as count FROM Album WHERE ArtistId = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM Artist WHERE ArtistId = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
} 