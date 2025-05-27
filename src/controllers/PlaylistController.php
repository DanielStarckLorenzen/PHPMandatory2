<?php

require_once 'src/models/Playlist.php';
require_once 'src/models/Track.php';

class PlaylistController extends BaseController {
    private $playlistModel;
    private $trackModel;
    
    public function __construct() {
        parent::__construct();
        $this->playlistModel = new Playlist();
        $this->trackModel = new Track();
    }
    
    /**
     * Get all playlists, optionally filtered by search term
     * 
     * @return void
     */
    public function getAll() {
        $params = $this->getRequestParams();
        $search = isset($params['s']) ? $params['s'] : null;
        
        $playlists = $this->playlistModel->getAll($search);
        $this->sendResponse($playlists);
    }
    
    /**
     * Get a single playlist by ID with its tracks
     * 
     * @param int $id Playlist ID
     * @return void
     */
    public function getOne($id) {
        $playlist = $this->playlistModel->getById($id);
        
        if (!$playlist) {
            $this->sendError("Playlist not found", 404);
            return;
        }
        
        $tracks = $this->playlistModel->getTracks($id);
        
        $response = [
            'playlist' => $playlist,
            'tracks' => $tracks
        ];
        
        $this->sendResponse($response);
    }
    
    /**
     * Create a new playlist
     * 
     * @return void
     */
    public function create() {
        $params = $this->getRequestParams();
        
        if (!isset($params['name']) || empty($params['name'])) {
            $this->sendError("Name is required");
            return;
        }
        
        $playlistId = $this->playlistModel->create($params['name']);
        
        if (!$playlistId) {
            $this->sendError("Failed to create playlist", 500);
            return;
        }
        
        $playlist = $this->playlistModel->getById($playlistId);
        $this->sendResponse($playlist, 201);
    }
    
    /**
     * Add a track to a playlist
     * 
     * @param int $id Playlist ID
     * @return void
     */
    public function addTrack($id) {
        $playlist = $this->playlistModel->getById($id);
        
        if (!$playlist) {
            $this->sendError("Playlist not found", 404);
            return;
        }
        
        $params = $this->getRequestParams();
        
        if (!isset($params['track_id']) || empty($params['track_id'])) {
            $this->sendError("Track ID is required");
            return;
        }
        
        $trackId = $params['track_id'];
        
        // Check if track exists
        $track = $this->trackModel->getById($trackId);
        if (!$track) {
            $this->sendError("Track not found", 404);
            return;
        }
        
        $success = $this->playlistModel->addTrack($id, $trackId);
        
        if (!$success) {
            $this->sendError("Track is already in the playlist", 400);
            return;
        }
        
        $this->sendResponse(['message' => 'Track added to playlist successfully']);
    }
    
    /**
     * Remove a track from a playlist
     * 
     * @param int $playlistId Playlist ID
     * @param int $trackId Track ID
     * @return void
     */
    public function removeTrack($playlistId, $trackId) {
        $playlist = $this->playlistModel->getById($playlistId);
        
        if (!$playlist) {
            $this->sendError("Playlist not found", 404);
            return;
        }
        
        $track = $this->trackModel->getById($trackId);
        if (!$track) {
            $this->sendError("Track not found", 404);
            return;
        }
        
        // Check if track is in playlist
        if (!$this->trackModel->existsInPlaylist($trackId, $playlistId)) {
            $this->sendError("Track is not in the playlist", 400);
            return;
        }
        
        $this->playlistModel->removeTrack($playlistId, $trackId);
        $this->sendResponse(['message' => 'Track removed from playlist successfully']);
    }
    
    /**
     * Delete a playlist
     * 
     * @param int $id Playlist ID
     * @return void
     */
    public function delete($id) {
        $playlist = $this->playlistModel->getById($id);
        
        if (!$playlist) {
            $this->sendError("Playlist not found", 404);
            return;
        }
        
        $success = $this->playlistModel->delete($id);
        
        if (!$success) {
            $this->sendError("Cannot delete playlist with tracks", 400);
            return;
        }
        
        $this->sendResponse(['message' => 'Playlist deleted successfully']);
    }
} 