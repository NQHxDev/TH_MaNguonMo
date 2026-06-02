<?php 

class AccountModel { 
    
    private PDO $conn; 
    
    private string $table_name = "account"; 
 
    public function __construct(PDO $db) { 
        $this->conn = $db; 
    } 
 
    public function getAccountByUsername(string $username) { 
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(['username' => $username]); 
        
        return $stmt->fetch(PDO::FETCH_OBJ); 
    }
 
    public function save(string $username, string $fullName, string $password, string $role = 'user'): bool { 
        if ($this->getAccountByUsername($username)) { 
            return false; 
        }
 
        $query = "INSERT INTO " . $this->table_name . " (username, fullname, password, role) VALUES (:username, :fullname, :password, :role)";
        $stmt = $this->conn->prepare($query);
 
        $username = htmlspecialchars(strip_tags($username)); 
        $fullName = htmlspecialchars(strip_tags($fullName)); 
        $passwordHash = password_hash($password, PASSWORD_BCRYPT); 
        $role = htmlspecialchars(strip_tags($role)); 
 
        return $stmt->execute([
            'username' => $username,
            'fullname' => $fullName,
            'password' => $passwordHash,
            'role'     => $role
        ]); 
    } 

    public function getAllAccounts(): array { 
        $query = "SELECT id, username, fullname, role FROM " . $this->table_name . " ORDER BY username ASC"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_OBJ); 
    } 

    public function updateRole(string $username, string $role): bool { 
        $query = "UPDATE " . $this->table_name . " SET role = :role WHERE username = :username"; 
        $stmt = $this->conn->prepare($query); 
        return $stmt->execute([
            'role' => $role,
            'username' => $username
        ]); 
    }
} 
?>