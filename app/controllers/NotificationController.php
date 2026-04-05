<?php
declare(strict_types=1);

class NotificationController extends Controller
{
    private INotificationService $notifService;

    public function __construct()
    {
        $this->notifService = new NotificationService();
    }

    public function index(): void
    {
        $this->requireAuth();

        $userId        = (int) $_SESSION['user_id'];
        $notifications = $this->notifService->getForUser($userId);
        $this->notifService->markAllRead($userId);

        $this->render('notifications/index', [
            'pageTitle'     => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    public function readAll(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $this->notifService->markAllRead((int) $_SESSION['user_id']);
        $this->redirect('/notifications');
    }

    public function clear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $this->notifService->deleteAll((int) $_SESSION['user_id']);
        $this->setFlash('success', 'All notifications cleared.');
        $this->redirect('/notifications');
    }
}
