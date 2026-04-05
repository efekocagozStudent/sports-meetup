<?php
declare(strict_types=1);

class ParticipantRepository implements IParticipantRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByEvent(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.username, u.email
             FROM participants p
             JOIN users u ON p.user_id = u.id
             WHERE p.event_id = ?
             ORDER BY p.status ASC, p.joined_at ASC'
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, e.id AS event_id, e.title, e.event_date, e.location,
                    s.name AS sport_name, s.icon AS sport_icon
             FROM participants p
             JOIN events e      ON p.event_id = e.id
             JOIN sport_types s ON e.sport_type_id = s.id
             WHERE p.user_id = ? AND e.status != "cancelled"
             ORDER BY e.event_date ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getStatus(int $eventId, int $userId): string|false
    {
        $stmt = $this->db->prepare(
            'SELECT status FROM participants WHERE event_id = ? AND user_id = ?'
        );
        $stmt->execute([$eventId, $userId]);
        $row = $stmt->fetch();
        return $row ? $row['status'] : false;
    }

    public function countApproved(int $eventId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM participants WHERE event_id = ? AND status = "approved"'
        );
        $stmt->execute([$eventId]);
        return (int) $stmt->fetchColumn();
    }

    public function getPendingForEvent(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.username
             FROM participants p
             JOIN users u ON p.user_id = u.id
             WHERE p.event_id = ? AND p.status = "pending"
             ORDER BY p.joined_at ASC'
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function getPendingForOrganizer(int $organizerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.username, e.title AS event_title, e.id AS event_id
             FROM participants p
             JOIN users u  ON p.user_id  = u.id
             JOIN events e ON p.event_id = e.id
             WHERE e.organizer_id = ? AND p.status = "pending"
             ORDER BY p.joined_at ASC'
        );
        $stmt->execute([$organizerId]);
        return $stmt->fetchAll();
    }

    public function join(int $eventId, int $userId, bool $requiresApproval): void
    {
        $status = $requiresApproval ? 'pending' : 'approved';
        $stmt   = $this->db->prepare(
            'INSERT INTO participants (event_id, user_id, status, joined_at)
             VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$eventId, $userId, $status]);
    }

    public function leave(int $eventId, int $userId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM participants WHERE event_id = ? AND user_id = ?'
        );
        $stmt->execute([$eventId, $userId]);
    }

    public function updateStatus(int $eventId, int $userId, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE participants SET status = ? WHERE event_id = ? AND user_id = ?'
        );
        $stmt->execute([$status, $eventId, $userId]);
    }
}
