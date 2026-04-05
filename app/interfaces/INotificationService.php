<?php
declare(strict_types=1);

interface INotificationService
{
    public function getForUser(int $userId): array;
    public function countUnread(int $userId): int;
    public function markAllRead(int $userId): void;
    public function deleteAll(int $userId): void;
    public function notifyJoin(int $organizerId, string $username, string $eventTitle, int $eventId, bool $requiresApproval): void;
    public function notifyLeave(int $organizerId, string $username, string $eventTitle, int $eventId): void;
    public function notifyApproval(int $userId, string $eventTitle, int $eventId): void;
    public function notifyRejection(int $userId, string $eventTitle, int $eventId): void;
    public function notifyCancellation(array $participants, string $eventTitle): void;
}
