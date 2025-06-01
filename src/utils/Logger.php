<?php

namespace Chinook\Utils;

class Logger {
    private $logFile;

    public function __construct() {
        $this->logFile = LOG_FILE;
        
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    // Log a message to file
    public function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
} 