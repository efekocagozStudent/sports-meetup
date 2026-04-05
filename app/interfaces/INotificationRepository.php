<?php
declare(strict_types=1);

interface INotificationRepository
{
    public function create(int $userId, string $type, string $message, string $link = ''): void;
    public function getForUser(int $userId, int $limit = 20): array;
    public function countUnread(int $userId): int;
    public function markAllRead(int $userId): void;
    public function markRead(int $id, int $userId): void;
    public function deleteAll(int $userId): void;
}
