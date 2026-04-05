<?php
declare(strict_types=1);

class UserService implements IUserService
{
    private IUserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    
    public function login(string $email, string $password): array|false
    {
        $user = $this->userRepo->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

        public function register(string $username, string $email, string $password, string $confirm): array
    {
        $errors = [];

        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be 3–50 characters.';
        }
        if (!ctype_alnum(str_replace(['_', '-'], '', $username))) {
            $errors[] = 'Username may only contain letters, numbers, underscores and hyphens.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            if ($this->userRepo->emailExists($email)) {
                $errors[] = 'That email is already registered.';
            }
            if ($this->userRepo->usernameExists($username)) {
                $errors[] = 'That username is already taken.';
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $id = $this->userRepo->create($username, $email, $password);
        return ['id' => $id];
    }

    public function findById(int $id): array|false
    {
        return $this->userRepo->findById($id);
    }

    public function allUsers(): array
    {
        return $this->userRepo->allUsers();
    }

    public function deleteUser(int $id): void
    {
        $this->userRepo->deleteById($id);
    }

    public function updateUser(int $id, string $username, string $email, string $role): void
    {
        $this->userRepo->updateById($id, $username, $email, $role);
    }

    public function requestPasswordReset(string $email): string|false
    {
        if (!$this->userRepo->findByEmail($email)) {
            return false;
        }
        $token = bin2hex(random_bytes(32));
        $this->userRepo->createResetToken($email, $token);
        return $token;
    }

    public function resetPassword(string $token, string $password, string $confirm): array
    {
        $errors = [];

        $row = $this->userRepo->findResetToken($token);
        if (!$row) {
            $errors[] = 'This reset link is invalid or has expired.';
            return ['errors' => $errors];
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $this->userRepo->updatePassword($row['email'], $password);
        return ['success' => true];
    }
}
