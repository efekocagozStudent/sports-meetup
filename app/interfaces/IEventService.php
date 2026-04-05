<?php
declare(strict_types=1);

interface IEventService
{
    public function listEvents(array $filters): array;
    public function getEvent(int $id): array|false;
    public function createEvent(array $data, int $organizerId): int;
    public function updateEvent(int $id, array $data): void;
    public function cancelEvent(int $id): void;
    public function getOrganizerEvents(int $userId): array;
    public function joinEvent(int $eventId, int $userId, string $username): string;
    public function leaveEvent(int $eventId, int $userId, string $username): void;
    public function approveParticipant(int $eventId, int $userId): void;
    public function rejectParticipant(int $eventId, int $userId): void;
    public function getParticipants(int $eventId): array;
    public function getApprovedCount(int $eventId): int;
    public function getParticipantStatus(int $eventId, int $userId): string|false;
    public function getJoinedEvents(int $userId): array;
    public function getPendingApprovals(int $organizerId): array;
    public function getSportTypes(): array;
    public function getEventsForApi(): array;
    public function validateEventData(array $data): array;
    public function allForAdmin(): array;
    public function adminDeleteEvent(int $id): void;
    public function adminUpdateEvent(int $id, array $data): void;
}
