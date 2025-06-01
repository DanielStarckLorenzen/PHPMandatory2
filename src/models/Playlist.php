<?php

namespace Chinook\Models;

use Chinook\Db\Database;

class Playlist {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all playlists
     * 
     * @param string|null $search Optional search term
     * @return array
     */
    public function getAll($search = null) {
        $sql = "SELECT p.PlaylistId, p.Name, COUNT(pt.TrackId) as TrackCount
                FROM Playlist p
                LEFT JOIN PlaylistTrack pt ON p.PlaylistId = pt.PlaylistId";
        
        $params = [];
        
        if ($search) {
            $sql .= " WHERE p.Name LIKE ?";
            $params[] = "%$search%";
        }
        
        $sql .= " GROUP BY p.PlaylistId, p.Name ORDER BY p.Name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get playlist by ID
     * 
     * @param int $id Playlist ID
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT PlaylistId, Name FROM Playlist WHERE PlaylistId = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Get tracks in a playlist
     * 
     * @param int $id Playlist ID
     * @return array
     */
    public function getTracks($id) {
        $sql = "SELECT t.TrackId, t.Name, t.Composer, t.Milliseconds, t.Bytes, t.UnitPrice,
                       a.AlbumId, a.Title as AlbumTitle,
                       ar.ArtistId, ar.Name as ArtistName,
                       g.GenreId, g.Name as GenreName,
                       mt.MediaTypeId, mt.Name as MediaTypeName
                FROM PlaylistTrack pt
                JOIN Track t ON pt.TrackId = t.TrackId
                LEFT JOIN Album a ON t.AlbumId = a.AlbumId
                LEFT JOIN Artist ar ON a.ArtistId = ar.ArtistId
                LEFT JOIN Genre g ON t.GenreId = g.GenreId
                LEFT JOIN MediaType mt ON t.MediaTypeId = mt.MediaTypeId
                WHERE pt.PlaylistId = ?
                ORDER BY t.Name";
        return $this->db->fetchAll($sql, [$id]);
    }
    
    /**
     * Create a new playlist
     * 
     * @param string $name Playlist name
     * @return int|string|false The new playlist ID or false on failure
     */
    public function create($name) {
        $sql = "SELECT MAX(PlaylistId) as maxId FROM Playlist";
        $result = $this->db->fetchOne($sql);
        $maxId = $result['maxId'];
        $newId = $maxId + 1;
        $sql = "INSERT INTO Playlist (PlaylistId, Name) VALUES (?, ?)";
        $this->db->query($sql, [$newId, $name]);
        return $newId;
    }
    
    /**
     * Add a track to a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @return bool
     */
    public function addTrack($playlistId, $trackId) {
        // Check if track is already in the playlist
        $sql = "SELECT COUNT(*) as count FROM PlaylistTrack WHERE PlaylistId = ? AND TrackId = ?";
        $result = $this->db->fetchOne($sql, [$playlistId, $trackId]);
        
        if ($result['count'] > 0) {
            return false; // Track already in playlist
        }
        
        $sql = "INSERT INTO PlaylistTrack (PlaylistId, TrackId) VALUES (?, ?)";
        $this->db->query($sql, [$playlistId, $trackId]);
        return true;
    }
    
    /**
     * Remove a track from a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @return bool
     */
    public function removeTrack($playlistId, $trackId) {
        $sql = "DELETE FROM PlaylistTrack WHERE PlaylistId = ? AND TrackId = ?";
        $this->db->query($sql, [$playlistId, $trackId]);
        return true;
    }
    
    /**
     * Delete a playlist
     * 
     * @param int $id Playlist ID
     * @return bool
     */
    public function delete($id) {
        // Check if playlist has tracks
        $sql = "SELECT COUNT(*) as count FROM PlaylistTrack WHERE PlaylistId = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM Playlist WHERE PlaylistId = ?";
        $this->db->query($sql, [$id]);
        return true;
    }
} 