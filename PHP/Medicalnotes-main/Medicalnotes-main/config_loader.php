<?php
/**
 * Configuration Loader
 * Loads environment variables from .env file
 */

class ConfigLoader {
    private static $instance = null;
    private $config = [];
    
    private function __construct() {
        $this->loadEnvFile();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ConfigLoader();
        }
        return self::$instance;
    }
    
    private function loadEnvFile() {
        $envFile = __DIR__ . '/.env';
        
        // If .env doesn't exist, try to copy from example
        if (!file_exists($envFile)) {
            $exampleFile = __DIR__ . '/.env.example';
            if (file_exists($exampleFile)) {
                copy($exampleFile, $envFile);
            } else {
                // Use default values
                $this->setDefaults();
                return;
            }
        }
        
        // Load .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            list($key, $value) = explode('=', $line, 2) + [NULL, NULL];
            if ($key && $value !== NULL) {
                $key = trim($key);
                $value = trim($value, '"\'');
                $this->config[$key] = $value;
                
                // Also set as environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
    
    private function setDefaults() {
        // Default configuration values
        $defaults = [
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'db2',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'DB_CHARSET' => 'utf8mb4',
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'MAX_UPLOAD_SIZE' => '52428800',
            'SESSION_LIFETIME' => '120'
        ];
        
        foreach ($defaults as $key => $value) {
            $this->config[$key] = $value;
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    
    public function get($key, $default = null) {
        return $this->config[$key] ?? $_ENV[$key] ?? getenv($key) ?? $default;
    }
    
    public function getDatabaseConfig() {
        return [
            'host' => $this->get('DB_HOST', 'localhost'),
            'database' => $this->get('DB_NAME', 'db2'),
            'username' => $this->get('DB_USERNAME', 'root'),
            'password' => $this->get('DB_PASSWORD', ''),
            'charset' => $this->get('DB_CHARSET', 'utf8mb4')
        ];
    }
    
    public function getApiKeys() {
        return [
            'assemblyai' => $this->get('ASSEMBLYAI_API_KEY', ''),
            'openai' => $this->get('OPENAI_API_KEY', ''),
            'google_ai' => $this->get('GOOGLE_AI_API_KEY', '')
        ];
    }
}

// Helper function for easy access
function config($key, $default = null) {
    return ConfigLoader::getInstance()->get($key, $default);
}

// Initialize configuration on include
ConfigLoader::getInstance();
?>