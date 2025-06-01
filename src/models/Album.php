<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class Album {
    private $db;
    
    public function __construct($database = null) {
        $this->db = $database ?? Database::getInstance();
    }
    
    /**
     * Get all albums with their artists
     * 
     * @param string|null $search Optional search term
     * @return array
     */
    public function getAll($search = null) {
        $sql = "SELECT a.AlbumId, a.Title, a.ArtistId, ar.Name as ArtistName 
                FROM Album a
                JOIN Artist ar ON a.ArtistId = ar.ArtistId";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE a.Title LIKE ?";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY a.Title";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get album by ID with artist info
     * 
     * @param int $id Album ID
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT a.AlbumId, a.Title, a.ArtistId, ar.Name as ArtistName 
                FROM Album a
                JOIN Artist ar ON a.ArtistId = ar.ArtistId
                WHERE a.AlbumId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get tracks in an album
     * 
     * @param int $id Album ID
     * @return array
     */
    public function getTracks($id) {
        $sql = "SELECT t.TrackId, t.Name, t.Composer, t.Milliseconds, t.Bytes, t.UnitPrice,
                       g.GenreId, g.Name as GenreName, 
                       mt.MediaTypeId, mt.Name as MediaTypeName
                FROM Track t
                LEFT JOIN Genre g ON t.GenreId = g.GenreId
                LEFT JOIN MediaType mt ON t.MediaTypeId = mt.MediaTypeId
                WHERE t.AlbumId = ?
                ORDER BY t.Name";
        return $this->db->fetchAll($sql, [$id]);
    }
    
    /**
     * Create a new album
     * 
     * @param string $title Album title
     * @param int $artistId Artist ID
     * @return int|string|false The new album ID or false on failure
     */
    public function create($title, $artistId) {
        $sql = "SELECT MAX(AlbumId) as maxId FROM Album";
        $result = $this->db->fetchOne($sql);
        $maxId = $result['maxId'];
        $newId = $maxId + 1;
        $sql = "INSERT INTO Album (AlbumId, Title, ArtistId) VALUES (?, ?, ?)";
        $this->db->query($sql, [$newId, $title, $artistId]);
        return $newId;
    }
    
    /**
     * Update an album
     * 
     * @param int $id Album ID
     * @param array $data Data to update
     * @return bool
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['title'])) {
            $fields[] = "Title = ?";
            $params[] = $data['title'];
        }
        
        if (isset($data['artist_id'])) {
            $fields[] = "ArtistId = ?";
            $params[] = $data['artist_id'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE Album SET " . implode(", ", $fields) . " WHERE AlbumId = ?";
        $this->db->query($sql, $params);
        return true;
    }
    
    /**
     * Delete an album
     * 
     * @param int $id Album ID
     * @return bool
     */
    public function delete($id) {
        // Check if album has tracks
        $sql = "SELECT COUNT(*) as count FROM Track WHERE AlbumId = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM Album WHERE AlbumId = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
} 