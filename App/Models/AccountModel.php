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
        // Nếu password rỗng (đăng nhập bằng social), lưu NULL thay vì hash chuỗi rỗng
        $passwordHash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null; 
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
    public function getAccountById(int $id) { 
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(['id' => $id]); 
        return $stmt->fetch(PDO::FETCH_OBJ); 
    }

    public function getSocialAccount(string $provider, string $providerUserId) {
        $query = "SELECT * FROM account_social WHERE provider = :provider AND provider_user_id = :provider_user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'provider' => $provider,
            'provider_user_id' => $providerUserId
        ]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function linkSocialAccount(int $accountId, string $provider, string $providerUserId, string $providerEmail, string $accessToken): bool {
        $query = "INSERT INTO account_social (account_id, provider, provider_user_id, provider_email, access_token) 
                  VALUES (:account_id, :provider, :provider_user_id, :provider_email, :access_token)
                  ON DUPLICATE KEY UPDATE access_token = VALUES(access_token), provider_email = VALUES(provider_email)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'account_id' => $accountId,
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'provider_email' => $providerEmail,
            'access_token' => $accessToken
        ]);
    }

    public function updateSocialAccessToken(int $id, string $accessToken): bool {
        $query = "UPDATE account_social SET access_token = :access_token WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'access_token' => $accessToken
        ]);
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $stmt->execute([
            'password' => $passwordHash,
            'id' => $id
        ]);
    }

    public function getLinkedSocialProviders(int $accountId): array {
        $query = "SELECT provider, provider_email, created_at FROM account_social WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['account_id' => $accountId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateProfile(int $id, string $fullname): bool {
        $query = "UPDATE " . $this->table_name . " SET fullname = :fullname WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'fullname' => htmlspecialchars(strip_tags($fullname)),
            'id' => $id
        ]);
    }
} 
?>