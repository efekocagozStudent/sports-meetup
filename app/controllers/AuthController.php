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

    public function forgotForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $this->render('auth/forgot', ['pageTitle' => 'Reset Password']);
    }

    public function forgotSubmit(): void
    {
        $this->verifyCsrf();

        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/forgot', [
                'pageTitle' => 'Reset Password',
                'errors'    => ['Enter a valid email address.'],
                'old'       => ['email' => $email],
            ]);
            return;
        }

        $token = $this->userService->requestPasswordReset($email);

        // Always show success message to avoid email enumeration
        // In production the token would be emailed; here we surface it for demo purposes
        $msg = 'If that email is registered, a reset link has been generated.';
        if ($token) {
            $resetUrl = url('/reset-password/' . $token);
            $msg .= ' Demo link: <a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '</a>';
        }

        $this->render('auth/forgot', [
            'pageTitle' => 'Reset Password',
            'success'   => $msg,
        ]);
    }

    public function resetForm(string $token): void
    {
        $this->render('auth/reset', [
            'pageTitle' => 'Set New Password',
            'token'     => $token,
        ]);
    }

    public function resetSubmit(string $token): void
    {
        $this->verifyCsrf();

        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $result = $this->userService->resetPassword($token, $password, $confirm);

        if (isset($result['errors'])) {
            $this->render('auth/reset', [
                'pageTitle' => 'Set New Password',
                'token'     => $token,
                'errors'    => $result['errors'],
            ]);
            return;
        }

        $this->setFlash('success', 'Password updated — please sign in with your new password.');
        $this->redirect('/login');
    }
}
