<?php

namespace Chinook\Controllers;

use Chinook\Models\Genre;
use Chinook\Controllers\BaseController;

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