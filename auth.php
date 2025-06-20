<?php
require_once 'config.php';

class AuthSystem {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }
    
    public function demoLogin() {
        $_SESSION['user'] = [
            'id' => 'demo-user',
            'name' => 'Demo User',
            'email' => 'demo@feedbackhub.com'
        ];
        return true;
    }
    
    public function logout() {
        session_destroy();
        header("Location: launch.php");
        exit();
    }
    
    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public function requireAuth() {
        if (!$this->getCurrentUser()) {
            header("Location: launch.php");
            exit();
        }
    }
}

$auth = new AuthSystem($db);
?>