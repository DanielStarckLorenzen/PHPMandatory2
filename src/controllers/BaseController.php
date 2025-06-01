<?php

namespace Chinook\Controllers;

use Chinook\Db\Database;

class BaseController {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Send JSON response with appropriate headers
    protected function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Send error response
    protected function sendError($message, $statusCode = 400) {
        $this->sendResponse(['error' => $message], $statusCode);
    }
    
    // Get request parameters from various sources
    protected function getRequestParams() {
        $params = [];
        
        // GET parameters
        if (!empty($_GET)) {
            $params = array_merge($params, $_GET);
        }
        
        // POST parameters
        if (!empty($_POST)) {
            $params = array_merge($params, $_POST);
        }
        
        // PUT parameters from input stream
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            parse_str(file_get_contents("php://input"), $putParams);
            if (!empty($putParams)) {
                $params = array_merge($params, $putParams);
            }
        }
        
        // Sanitize all parameters
        foreach ($params as $key => $value) {
            $params[$key] = $this->sanitizeInput($value);
        }
        
        return $params;
    }
    
    // Sanitize input to prevent XSS and other attacks
    protected function sanitizeInput($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitizeInput($value);
            }
            return $input;
        }
        
        // Convert special characters to HTML entities
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
} 