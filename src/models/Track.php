<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class Track {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Search tracks by name or get tracks by composer
    public function search($search = null, $composer = null) {
        $sql = "SELECT t.TrackId, t.Name, t.AlbumId, t.Composer, t.Milliseconds, t.Bytes, t.UnitPrice,
                       a.Title as AlbumTitle, ar.Name as ArtistName,
                       g.GenreId, g.Name as GenreName, 
                       mt.MediaTypeId, mt.Name as MediaTypeName
                FROM Track t
                LEFT JOIN Album a ON t.AlbumId = a.AlbumId
                LEFT JOIN Artist ar ON a.ArtistId = ar.ArtistId
                LEFT JOIN Genre g ON t.GenreId = g.GenreId
                LEFT JOIN MediaType mt ON t.MediaTypeId = mt.MediaTypeId";
        
        $params = [];
        $conditions = [];
        
        if ($search) {
            $conditions[] = "t.Name LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($composer) {
            $conditions[] = "t.Composer LIKE ?";
            $params[] = "%$composer%";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY t.Name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get track by ID
    public function getById($id) {
        $sql = "SELECT t.TrackId, t.Name, t.AlbumId, t.Composer, t.Milliseconds, t.Bytes, t.UnitPrice,
                       a.Title as AlbumTitle, ar.Name as ArtistName,
                       g.GenreId, g.Name as GenreName, 
                       mt.MediaTypeId, mt.Name as MediaTypeName
                FROM Track t
                LEFT JOIN Album a ON t.AlbumId = a.AlbumId
                LEFT JOIN Artist ar ON a.ArtistId = ar.ArtistId
                LEFT JOIN Genre g ON t.GenreId = g.GenreId
                LEFT JOIN MediaType mt ON t.MediaTypeId = mt.MediaTypeId
                WHERE t.TrackId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    // Create a new track
    public function create($data) {
        $sql = "SELECT MAX(TrackId) as maxId FROM Track";
        $result = $this->db->fetchOne($sql);
        $maxId = $result['maxId'];
        $newId = $maxId + 1;

        $sql = "INSERT INTO Track (TrackId, Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $newId,
            $data['name'],
            $data['album_id'],
            $data['media_type_id'],
            $data['genre_id'],
            $data['composer'],
            $data['milliseconds'],
            $data['bytes'],
            $data['unit_price']
        ];
        
        $this->db->query($sql, $params);
        return $newId;
    }
    
    // Update a track
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $possibleFields = [
            'name' => 'Name',
            'album_id' => 'AlbumId',
            'media_type_id' => 'MediaTypeId',
            'genre_id' => 'GenreId',
            'composer' => 'Composer',
            'milliseconds' => 'Milliseconds',
            'bytes' => 'Bytes',
            'unit_price' => 'UnitPrice'
        ];
        
        foreach ($possibleFields as $key => $field) {
            if (isset($data[$key])) {
                $fields[] = "$field = ?";
                $params[] = $data[$key];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE Track SET " . implode(", ", $fields) . " WHERE TrackId = ?";
        $this->db->query($sql, $params);
        return true;
    }
    
    // Delete a track
    public function delete($id) {
        // Check if track is in any playlist
        $sql = "SELECT COUNT(*) as count FROM PlaylistTrack WHERE TrackId = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM Track WHERE TrackId = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
    
    // Check if a track exists in a playlist
    public function existsInPlaylist($trackId, $playlistId) {
        $sql = "SELECT COUNT(*) as count FROM PlaylistTrack WHERE PlaylistId = ? AND TrackId = ?";
        $result = $this->db->fetchOne($sql, [$playlistId, $trackId]);
        return $result['count'] > 0;
    }
} 