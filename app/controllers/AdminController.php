<?php
declare(strict_types=1);

class AdminController extends Controller
{
    private IEventService $eventService;
    private IUserService  $userService;

    public function __construct()
    {
        $this->eventService = new EventService();
        $this->userService  = new UserService();
    }

    public function dashboard(): void
    {
        $this->requireAdmin();

        $this->render('admin/dashboard', [
            'pageTitle' => 'Admin Panel',
            'users'     => $this->userService->allUsers(),
            'events'    => $this->eventService->allForAdmin(),
        ]);
    }

    // ── Users ─────────────────────────────────────────────────────────────

    public function editUser(string $id): void
    {
        $this->requireAdmin();

        $user = $this->userService->findById((int) $id);
        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/admin');
        }

        $this->render('admin/edit_user', [
            'pageTitle' => 'Edit User: ' . $user['username'],
            'user'      => $user,
        ]);
    }

    public function updateUser(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $userId   = (int) $id;
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $role     = in_array($_POST['role'] ?? '', ['user', 'admin'], true)
            ? $_POST['role']
            : 'user';

        $errors = [];
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be 3–50 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if ($userId === (int) $_SESSION['user_id'] && $role !== 'admin') {
            $errors[] = 'You cannot remove admin role from your own account.';
        }

        if (!empty($errors)) {
            $user = $this->userService->findById($userId);
            $this->render('admin/edit_user', [
                'pageTitle' => 'Edit User',
                'user'      => array_merge($user ?: [], ['username' => $username, 'email' => $email, 'role' => $role]),
                'errors'    => $errors,
            ]);
            return;
        }

        $this->userService->updateUser($userId, $username, $email, $role);
        $this->setFlash('success', 'User updated.');
        $this->redirect('/admin');
    }

    public function deleteUser(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $userId = (int) $id;

        if ($userId === (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot delete your own admin account.');
            $this->redirect('/admin');
        }

        $this->userService->deleteUser($userId);
        $this->setFlash('success', 'User deleted.');
        $this->redirect('/admin');
    }

    // ── Events ────────────────────────────────────────────────────────────

    public function createEvent(): void
    {
        $this->requireAdmin();

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('admin/create_event', [
            'pageTitle'  => 'Admin: Create Event',
            'sportTypes' => $sportTypes,
        ]);
    }

    public function storeEvent(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $data   = $this->extractEventData();
        $errors = $this->eventService->validateEventData($data);

        if (empty($errors)) {
            $id = $this->eventService->createEvent($data, (int) $_SESSION['user_id']);
            $this->setFlash('success', 'Event created.');
            $this->redirect('/admin');
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('admin/create_event', [
            'pageTitle'  => 'Admin: Create Event',
            'sportTypes' => $sportTypes,
            'errors'     => $errors,
            'old'        => $data,
        ]);
    }

    public function editEvent(string $id): void
    {
        $this->requireAdmin();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Event not found.');
            $this->redirect('/admin');
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('admin/edit_event', [
            'pageTitle'  => 'Admin: Edit — ' . $event['title'],
            'event'      => $event,
            'sportTypes' => $sportTypes,
        ]);
    }

    public function updateEvent(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Event not found.');
            $this->redirect('/admin');
        }

        $data   = $this->extractEventData();
        $errors = $this->eventService->validateEventData($data);

        if (empty($errors)) {
            $allowed        = ['open', 'closed', 'cancelled'];
            $data['status'] = in_array($_POST['status'] ?? '', $allowed, true)
                ? $_POST['status']
                : $event['status'];

            $this->eventService->adminUpdateEvent((int) $id, $data);
            $this->setFlash('success', 'Event updated.');
            $this->redirect('/admin');
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('admin/edit_event', [
            'pageTitle'  => 'Admin: Edit — ' . $event['title'],
            'event'      => array_merge($event, $data),
            'sportTypes' => $sportTypes,
            'errors'     => $errors,
        ]);
    }

    public function deleteEvent(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $this->eventService->adminDeleteEvent((int) $id);
        $this->setFlash('success', 'Event permanently deleted.');
        $this->redirect('/admin');
    }

    private function extractEventData(): array
    {
        $allowed = ['beginner', 'intermediate', 'advanced'];
        $skill   = $_POST['skill_level'] ?? 'beginner';
        return [
            'title'             => trim($_POST['title']           ?? ''),
            'description'       => trim($_POST['description']     ?? ''),
            'sport_type_id'     => trim($_POST['sport_type_id']   ?? ''),
            'event_date'        => trim($_POST['event_date']      ?? ''),
            'location'          => trim($_POST['location']        ?? ''),
            'max_participants'  => trim($_POST['max_participants'] ?? ''),
            'requires_approval' => !empty($_POST['requires_approval']),
            'skill_level'       => in_array($skill, $allowed, true) ? $skill : 'beginner',
        ];
    }
}

