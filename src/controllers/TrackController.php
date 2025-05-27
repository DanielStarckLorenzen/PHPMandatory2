<?php

require_once 'src/models/Track.php';
require_once 'src/models/Album.php';
require_once 'src/models/Genre.php';
require_once 'src/models/MediaType.php';

class TrackController extends BaseController {
    private $trackModel;
    private $albumModel;
    private $genreModel;
    private $mediaTypeModel;
    
    public function __construct() {
        parent::__construct();
        $this->trackModel = new Track();
        $this->albumModel = new Album();
        $this->genreModel = new Genre();
        $this->mediaTypeModel = new MediaType();
    }
    
    /**
     * Search tracks by name or get tracks by composer
     * 
     * @return void
     */
    public function search() {
        $params = $this->getRequestParams();
        $search = isset($params['s']) ? $params['s'] : null;
        $composer = isset($params['composer']) ? $params['composer'] : null;
        
        $tracks = $this->trackModel->search($search, $composer);
        $this->sendResponse($tracks);
    }
    
    /**
     * Get a single track by ID
     * 
     * @param int $id Track ID
     * @return void
     */
    public function getOne($id) {
        $track = $this->trackModel->getById($id);
        
        if (!$track) {
            $this->sendError("Track not found", 404);
            return;
        }
        
        $this->sendResponse($track);
    }
    
    /**
     * Create a new track
     * 
     * @return void
     */
    public function create() {
        $params = $this->getRequestParams();
        
        // Check required fields
        $requiredFields = [
            'name', 'album_id', 'media_type_id', 'genre_id', 
            'composer', 'milliseconds', 'bytes', 'unit_price'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($params[$field]) || $params[$field] === '') {
                $this->sendError("$field is required");
                return;
            }
        }
        
        // Validate numeric fields
        $numericFields = ['album_id', 'media_type_id', 'genre_id', 'milliseconds', 'bytes', 'unit_price'];
        foreach ($numericFields as $field) {
            if (!is_numeric($params[$field])) {
                $this->sendError("$field must be numeric");
                return;
            }
        }
        
        // Check if album exists
        $album = $this->albumModel->getById($params['album_id']);
        if (!$album) {
            $this->sendError("Album not found", 404);
            return;
        }
        
        // Check if media type exists
        $mediaType = $this->mediaTypeModel->getById($params['media_type_id']);
        if (!$mediaType) {
            $this->sendError("Media type not found", 404);
            return;
        }
        
        // Check if genre exists
        $genre = $this->genreModel->getById($params['genre_id']);
        if (!$genre) {
            $this->sendError("Genre not found", 404);
            return;
        }
        
        $trackId = $this->trackModel->create($params);
        
        if (!$trackId) {
            $this->sendError("Failed to create track", 500);
            return;
        }
        
        $track = $this->trackModel->getById($trackId);
        $this->sendResponse($track, 201);
    }
    
    /**
     * Update a track
     * 
     * @param int $id Track ID
     * @return void
     */
    public function update($id) {
        $track = $this->trackModel->getById($id);
        
        if (!$track) {
            $this->sendError("Track not found", 404);
            return;
        }
        
        $params = $this->getRequestParams();
        $data = [];
        
        // Check and validate fields
        $possibleFields = [
            'name', 'album_id', 'media_type_id', 'genre_id', 
            'composer', 'milliseconds', 'bytes', 'unit_price'
        ];
        
        $numericFields = ['album_id', 'media_type_id', 'genre_id', 'milliseconds', 'bytes', 'unit_price'];
        
        foreach ($possibleFields as $field) {
            if (isset($params[$field]) && $params[$field] !== '') {
                // Validate numeric fields
                if (in_array($field, $numericFields) && !is_numeric($params[$field])) {
                    $this->sendError("$field must be numeric");
                    return;
                }
                
                $data[$field] = $params[$field];
            }
        }
        
        // Check if album exists if provided
        if (isset($data['album_id'])) {
            $album = $this->albumModel->getById($data['album_id']);
            if (!$album) {
                $this->sendError("Album not found", 404);
                return;
            }
        }
        
        // Check if media type exists if provided
        if (isset($data['media_type_id'])) {
            $mediaType = $this->mediaTypeModel->getById($data['media_type_id']);
            if (!$mediaType) {
                $this->sendError("Media type not found", 404);
                return;
            }
        }
        
        // Check if genre exists if provided
        if (isset($data['genre_id'])) {
            $genre = $this->genreModel->getById($data['genre_id']);
            if (!$genre) {
                $this->sendError("Genre not found", 404);
                return;
            }
        }
        
        if (empty($data)) {
            $this->sendError("No data provided for update");
            return;
        }
        
        $success = $this->trackModel->update($id, $data);
        
        if (!$success) {
            $this->sendError("Failed to update track", 500);
            return;
        }
        
        $track = $this->trackModel->getById($id);
        $this->sendResponse($track);
    }
    
    /**
     * Delete a track
     * 
     * @param int $id Track ID
     * @return void
     */
    public function delete($id) {
        $track = $this->trackModel->getById($id);
        
        if (!$track) {
            $this->sendError("Track not found", 404);
            return;
        }
        
        $success = $this->trackModel->delete($id);
        
        if (!$success) {
            $this->sendError("Cannot delete track that belongs to a playlist", 400);
            return;
        }
        
        $this->sendResponse(['message' => 'Track deleted successfully']);
    }
} 