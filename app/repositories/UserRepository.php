<?php
declare(strict_types=1);

class UserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, email, role, created_at FROM users WHERE id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create(string $username, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password_hash, created_at)
             VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$username, $email, $hash]);
        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return (bool) $stmt->fetch();
    }

    public function allUsers(): array
    {
        return $this->db->query(
            'SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ? AND role != \'admin\'');
        $stmt->execute([$id]);
    }

    public function updateById(int $id, string $username, string $email, string $role): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?'
        );
        $stmt->execute([$username, $email, $role, $id]);
    }
}
