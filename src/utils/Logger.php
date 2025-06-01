<?php

namespace Chinook\Utils;

class Logger {
    private $logFile;

    public function __construct() {
        $this->logFile = LOG_FILE;
        
        // Create log directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Log a message with timestamp
     * 
     * @param string $message The message to log
     * @return void
     */
    public function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        
        // Append to log file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
} 