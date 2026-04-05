<?php
declare(strict_types=1);

interface IUserService
{
    public function login(string $email, string $password): array|false;
    public function register(string $username, string $email, string $password, string $confirm): array;
    public function findById(int $id): array|false;
    public function allUsers(): array;
    public function deleteUser(int $id): void;
    public function updateUser(int $id, string $username, string $email, string $role): void;
}
