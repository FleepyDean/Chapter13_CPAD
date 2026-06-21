<?php 
namespace App; 
  
use PDO; 
use PDOException; 
  
final class Database
{ 
    private static ?PDO $pdo = null; 
  
    public static function get(): PDO 
    { 
        if (self::$pdo) return self::$pdo; 
  
        $dsn = sprintf( 
            'mysql:host=%s;port=%s;dbname=%s;charset=%s', 
            $_ENV['DB_HOST']    ?? '127.0.0.1', 
            $_ENV['DB_PORT']    ?? '3306', 
            $_ENV['DB_NAME']    ?? 'books_api', 
            $_ENV['DB_CHARSET'] ?? 'utf8mb4' 
        ); 
        $options = [ 
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            PDO::ATTR_EMULATE_PREPARES   => false, 
        ]; 

        if (!empty($_ENV['DB_SSL_CA'])) { 
            $options[PDO::MYSQL_ATTR_SSL_CA] = $_ENV['DB_SSL_CA']; 
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true; 
        } elseif (($_ENV['DB_SSL_MODE'] ?? '') === 'require') { 
            $caFile = '/etc/ssl/certs/ca-certificates.crt'; 
            if (file_exists($caFile)) { 
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile; 
            } 
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false; 
        } 

        try { 
            self::$pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', $options); 
        } catch (PDOException $e) { 
            error_log('[DB] ' . $e->getMessage()); 
            throw new \RuntimeException('Database connection failed', 500, $e); 
        } 
        return self::$pdo; 
    } 
} 