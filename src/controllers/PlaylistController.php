<?php

namespace Chinook\Controllers;

use Chinook\Models\Playlist;
use Chinook\Models\Track;
use Chinook\Controllers\BaseController;

class PlaylistController extends BaseController {
    private $playlistModel;
    private $trackModel;
    
    public function __construct() {
        parent::__construct();
        $this->playlistModel = new Playlist();
        $this->trackModel = new Track();
    }
    
    // Get all playlists, optionally filtered by search term
    public function getAll() {
        $params = $this->getRequestParams();
        $search = isset($params['s']) ? $params['s'] : null;
        
        $playlists = $this->playlistModel->getAll($search);
        $this->sendResponse($playlists);
    }
    
    // Get a single playlist by ID with its tracks
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
    
    // Create a new playlist
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
    
    // Add a track to a playlist
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
    
    // Remove a track from a playlist
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
    
    // Delete a playlist
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