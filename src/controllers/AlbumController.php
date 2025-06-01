<?php

namespace Chinook\Controllers;

use Chinook\Models\Album;
use Chinook\Models\Artist;
use Chinook\Controllers\BaseController;

class AlbumController extends BaseController {
    private $albumModel;
    private $artistModel;
    
    public function __construct() {
        parent::__construct();
        $this->albumModel = new Album();
        $this->artistModel = new Artist();
    }
    
    // Get all albums, optionally filtered by search term
    public function getAll() {
        $params = $this->getRequestParams();
        $search = isset($params['s']) ? $params['s'] : null;
        
        $albums = $this->albumModel->getAll($search);
        $this->sendResponse($albums);
    }
    
    // Get a single album by ID
    public function getOne($id) {
        $album = $this->albumModel->getById($id);
        
        if (!$album) {
            $this->sendError("Album not found", 404);
            return;
        }
        
        $this->sendResponse($album);
    }
    
    // Get all tracks in an album
    public function getTracks($id) {
        $album = $this->albumModel->getById($id);
        
        if (!$album) {
            $this->sendError("Album not found", 404);
            return;
        }
        
        $tracks = $this->albumModel->getTracks($id);
        $this->sendResponse($tracks);
    }
    
    // Create a new album
    public function create() {
        $params = $this->getRequestParams();
        
        if (!isset($params['title']) || empty($params['title'])) {
            $this->sendError("Title is required");
            return;
        }
        
        if (!isset($params['artist_id']) || empty($params['artist_id'])) {
            $this->sendError("Artist ID is required");
            return;
        }
        
        // Check if artist exists
        $artist = $this->artistModel->getById($params['artist_id']);
        if (!$artist) {
            $this->sendError("Artist not found", 404);
            return;
        }
        
        $albumId = $this->albumModel->create($params['title'], $params['artist_id']);
        
        if (!$albumId) {
            $this->sendError("Failed to create album", 500);
            return;
        }
        
        $album = $this->albumModel->getById($albumId);
        $this->sendResponse($album, 201);
    }
    
    // Update an album
    public function update($id) {
        $album = $this->albumModel->getById($id);
        
        if (!$album) {
            $this->sendError("Album not found", 404);
            return;
        }
        
        $params = $this->getRequestParams();
        $data = [];
        
        if (isset($params['title']) && !empty($params['title'])) {
            $data['title'] = $params['title'];
        }
        
        if (isset($params['artist_id']) && !empty($params['artist_id'])) {
            // Check if artist exists
            $artist = $this->artistModel->getById($params['artist_id']);
            if (!$artist) {
                $this->sendError("Artist not found", 404);
                return;
            }
            
            $data['artist_id'] = $params['artist_id'];
        }
        
        if (empty($data)) {
            $this->sendError("No data provided for update");
            return;
        }
        
        $success = $this->albumModel->update($id, $data);
        
        if (!$success) {
            $this->sendError("Failed to update album", 500);
            return;
        }
        
        $album = $this->albumModel->getById($id);
        $this->sendResponse($album);
    }
    
    // Delete an album
    public function delete($id) {
        $album = $this->albumModel->getById($id);
        
        if (!$album) {
            $this->sendError("Album not found", 404);
            return;
        }
        
        $success = $this->albumModel->delete($id);
        
        if (!$success) {
            $this->sendError("Cannot delete album with tracks", 400);
            return;
        }
        
        $this->sendResponse(['message' => 'Album deleted successfully']);
    }
} 