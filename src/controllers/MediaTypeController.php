<?php

namespace Chinook\Controllers;

use Chinook\Models\MediaType;
use Chinook\Controllers\BaseController;

class MediaTypeController extends BaseController {
    private $mediaTypeModel;
    
    public function __construct() {
        parent::__construct();
        $this->mediaTypeModel = new MediaType();
    }
    
    /**
     * Get all media types
     * 
     * @return void
     */
    public function getAll() {
        $mediaTypes = $this->mediaTypeModel->getAll();
        $this->sendResponse($mediaTypes);
    }
} 