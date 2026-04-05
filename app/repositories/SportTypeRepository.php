<?php
declare(strict_types=1);

class SportTypeRepository implements ISportTypeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM sport_types ORDER BY name')
                        ->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM sport_types WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
