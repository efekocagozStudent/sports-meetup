<?php
declare(strict_types=1);

class AuthController extends Controller
{
    private IUserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/login', ['pageTitle' => 'Sign In']);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $errors   = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            $user = $this->userService->login($email, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'] ?? 'user';

                $redirect = $_SESSION['redirect_after_login'] ?? null;
                unset($_SESSION['redirect_after_login']);

                $this->setFlash('success', 'Welcome back, ' . $user['username'] . '!');
                $this->redirect($redirect ? ltrim(str_replace(BASE_URL, '', $redirect), '') : '/dashboard');
                return;
            }
            $errors[] = 'Invalid email or password.';
        }

        $this->render('auth/login', [
            'pageTitle' => 'Sign In',
            'errors'    => $errors,
            'old'       => ['email' => $email],
        ]);
    }

    public function registerForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/register', ['pageTitle' => 'Create Account']);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $username = trim($_POST['username']         ?? '');
        $email    = trim($_POST['email']            ?? '');
        $password = $_POST['password']              ?? '';
        $confirm  = $_POST['confirm_password']      ?? '';

        $result = $this->userService->register($username, $email, $password, $confirm);

        if (isset($result['errors'])) {
            $this->render('auth/register', [
                'pageTitle' => 'Create Account',
                'errors'    => $result['errors'],
                'old'       => ['username' => $username, 'email' => $email],
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']  = $result['id'];
        $_SESSION['username'] = $username;

        $this->setFlash('success', 'Account created — welcome to SportsMeet!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
