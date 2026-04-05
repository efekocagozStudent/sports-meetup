<?php
declare(strict_types=1);

class EventRepository implements IEventRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(array $filters = []): array
    {
        $where  = ["e.status != 'cancelled'"];
        $params = [];

        if (!empty($filters['sport_type_id'])) {
            $where[]  = 'e.sport_type_id = ?';
            $params[] = (int) $filters['sport_type_id'];
        }

        if (!empty($filters['search'])) {
            $where[]  = '(e.title LIKE ? OR e.location LIKE ?)';
            $like     = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($filters['upcoming'])) {
            $where[] = 'e.event_date >= NOW()';
        }

        $sql = 'SELECT e.*,
                       s.name       AS sport_name,
                       s.icon       AS sport_icon,
                       u.username   AS organizer_name,
                       (SELECT COUNT(*) FROM participants p
                        WHERE p.event_id = e.id AND p.status = "approved") AS participant_count
                FROM events e
                JOIN sport_types s ON e.sport_type_id = s.id
                JOIN users u       ON e.organizer_id  = u.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY e.event_date ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT e.*,
                    s.name        AS sport_name,
                    s.icon        AS sport_icon,
                    s.min_players,
                    s.max_players,
                    u.username    AS organizer_name
             FROM events e
             JOIN sport_types s ON e.sport_type_id = s.id
             JOIN users u       ON e.organizer_id  = u.id
             WHERE e.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO events
                (title, description, sport_type_id, organizer_id, event_date,
                 location, max_participants, requires_approval, skill_level, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "open", NOW())'
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            (int) $data['sport_type_id'],
            (int) $data['organizer_id'],
            $data['event_date'],
            $data['location'],
            (int) $data['max_participants'],
            $data['requires_approval'] ? 1 : 0,
            $data['skill_level'] ?? 'beginner',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE events
             SET title = ?, description = ?, sport_type_id = ?, event_date = ?,
                 location = ?, max_participants = ?, requires_approval = ?, skill_level = ?, status = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            (int) $data['sport_type_id'],
            $data['event_date'],
            $data['location'],
            (int) $data['max_participants'],
            $data['requires_approval'] ? 1 : 0,
            $data['skill_level'] ?? 'beginner',
            $data['status'],
            $id,
        ]);
    }

    public function cancel(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE events SET status = "cancelled" WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function getByOrganizer(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*,
                    s.name AS sport_name,
                    s.icon AS sport_icon,
                    (SELECT COUNT(*) FROM participants p
                     WHERE p.event_id = e.id AND p.status = "approved") AS participant_count
             FROM events e
             JOIN sport_types s ON e.sport_type_id = s.id
             WHERE e.organizer_id = ?
             ORDER BY e.event_date DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function forApi(): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.id, e.title, e.description, e.event_date, e.location,
                    e.max_participants, e.status, e.skill_level, e.requires_approval,
                    s.name AS sport, s.icon AS sport_icon,
                    u.username AS organizer,
                    (SELECT COUNT(*) FROM participants p
                     WHERE p.event_id = e.id AND p.status = "approved") AS participants_count
             FROM events e
             JOIN sport_types s ON e.sport_type_id = s.id
             JOIN users u       ON e.organizer_id  = u.id
             WHERE e.status != "cancelled"
             ORDER BY e.event_date ASC'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();

        // Add computed spots_left field
        foreach ($rows as &$row) {
            $row['spots_left'] = max(0, (int) $row['max_participants'] - (int) $row['participants_count']);
        }
        return $rows;
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query(
            'SELECT e.*,
                    s.name     AS sport_name,
                    s.icon     AS sport_icon,
                    u.username AS organizer_name,
                    (SELECT COUNT(*) FROM participants p
                     WHERE p.event_id = e.id AND p.status = "approved") AS participant_count
             FROM events e
             JOIN sport_types s ON e.sport_type_id = s.id
             JOIN users u       ON e.organizer_id  = u.id
             ORDER BY e.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([$id]);
    }
}
