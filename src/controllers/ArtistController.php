<?php

namespace Chinook\Controllers;

use Chinook\Models\Artist;
use Chinook\Controllers\BaseController;

class ArtistController extends BaseController {
    private $artistModel;
    
    public function __construct() {
        parent::__construct();
        $this->artistModel = new Artist();
    }
    
    // Get all artists, optionally filtered by search term
    public function getAll() {
        $params = $this->getRequestParams();
        $search = isset($params['s']) ? $params['s'] : null;
        
        $artists = $this->artistModel->getAll($search);
        $this->sendResponse($artists);
    }
    
    // Get a single artist by ID
    public function getOne($id) {
        $artist = $this->artistModel->getById($id);
        
        if (!$artist) {
            $this->sendError("Artist not found", 404);
            return;
        }
        
        $this->sendResponse($artist);
    }
    
    // Get all albums by an artist
    public function getAlbums($id) {
        $artist = $this->artistModel->getById($id);
        
        if (!$artist) {
            $this->sendError("Artist not found", 404);
            return;
        }
        
        $albums = $this->artistModel->getAlbums($id);
        $this->sendResponse($albums);
    }
    
    // Create a new artist
    public function create() {
        $params = $this->getRequestParams();
        
        if (!isset($params['name']) || empty($params['name'])) {
            $this->sendError("Name is required");
            return;
        }
        
        $artistId = $this->artistModel->create($params['name']);
        
        if (!$artistId) {
            $this->sendError("Failed to create artist", 500);
            return;
        }
        
        $artist = $this->artistModel->getById($artistId);
        $this->sendResponse($artist, 201);
    }
    
    // Delete an artist
    public function delete($id) {
        $artist = $this->artistModel->getById($id);
        
        if (!$artist) {
            $this->sendError("Artist not found", 404);
            return;
        }
        
        $success = $this->artistModel->delete($id);
        
        if (!$success) {
            $this->sendError("Cannot delete artist with albums", 400);
            return;
        }
        
        $this->sendResponse(['message' => 'Artist deleted successfully']);
    }
} 