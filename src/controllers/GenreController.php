<?php

require_once 'src/models/Genre.php';

class GenreController extends BaseController {
    private $genreModel;
    
    public function __construct() {
        parent::__construct();
        $this->genreModel = new Genre();
    }
    
    /**
     * Get all genres
     * 
     * @return void
     */
    public function getAll() {
        $genres = $this->genreModel->getAll();
        $this->sendResponse($genres);
    }
} 