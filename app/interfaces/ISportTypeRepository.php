<?php
declare(strict_types=1);

interface ISportTypeRepository
{
    public function all(): array;
    public function findById(int $id): array|false;
}
