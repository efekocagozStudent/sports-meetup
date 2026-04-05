<?php
declare(strict_types=1);

interface IEventRepository
{
    public function all(array $filters = []): array;
    public function findById(int $id): array|false;
    public function create(array $data): int;
    public function update(int $id, array $data): void;
    public function cancel(int $id): void;
    public function getByOrganizer(int $userId): array;
    public function forApi(): array;
    public function allForAdmin(): array;
    public function deleteById(int $id): void;
}
