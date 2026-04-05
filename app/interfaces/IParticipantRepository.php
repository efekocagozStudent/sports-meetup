<?php
declare(strict_types=1);

interface IParticipantRepository
{
    public function getByEvent(int $eventId): array;
    public function getByUser(int $userId): array;
    public function getStatus(int $eventId, int $userId): string|false;
    public function countApproved(int $eventId): int;
    public function getPendingForEvent(int $eventId): array;
    public function getPendingForOrganizer(int $organizerId): array;
    public function join(int $eventId, int $userId, bool $requiresApproval): void;
    public function leave(int $eventId, int $userId): void;
    public function updateStatus(int $eventId, int $userId, string $status): void;
}
