<?php
declare(strict_types=1);

class DashboardController extends Controller
{
    private IEventService $eventService;

    public function __construct()
    {
        $this->eventService = new EventService();
    }

    public function index(): void
    {
        $this->requireAuth();

        $userId = (int) $_SESSION['user_id'];

        $this->render('dashboard/index', [
            'pageTitle'        => 'My Dashboard',
            'organizedEvents'  => $this->eventService->getOrganizerEvents($userId),
            'joinedEvents'     => $this->eventService->getJoinedEvents($userId),
            'pendingApprovals' => $this->eventService->getPendingApprovals($userId),
        ]);
    }
}
