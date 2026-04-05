<?php
declare(strict_types=1);

interface IUserRepository
{
    public function findById(int $id): array|false;
    public function findByEmail(string $email): array|false;
    public function create(string $username, string $email, string $password): int;
    public function emailExists(string $email): bool;
    public function usernameExists(string $username): bool;
    public function allUsers(): array;
    public function deleteById(int $id): void;
    public function updateById(int $id, string $username, string $email, string $role): void;
}
