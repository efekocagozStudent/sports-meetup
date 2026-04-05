<?php
declare(strict_types=1);

class EventController extends Controller
{
    private IEventService $eventService;

    public function __construct()
    {
        $this->eventService = new EventService();
    }

    public function index(): void
    {
        $filters = [
            'sport_type_id' => $_GET['sport']    ?? '',
            'search'        => trim($_GET['search'] ?? ''),
            'upcoming'      => !empty($_GET['upcoming']),
        ];

        $events     = $this->eventService->listEvents($filters);
        $sportTypes = $this->eventService->getSportTypes();

        $this->render('events/index', [
            'pageTitle'  => 'Find Events',
            'cards'      => EventCardViewModel::fromRows($events),
            'sportTypes' => $sportTypes,
            'filters'    => $filters,
        ]);
    }

    public function explore(): void
    {
        $sportTypes = $this->eventService->getSportTypes();
        $this->render('events/explore', [
            'pageTitle'  => 'Live Explorer',
            'sportTypes' => $sportTypes,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $sportTypes = $this->eventService->getSportTypes();
        $this->render('events/create', [
            'pageTitle'  => 'Create Event',
            'sportTypes' => $sportTypes,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $data   = $this->extractEventData();
        $errors = $this->eventService->validateEventData($data);

        if (empty($errors)) {
            $id = $this->eventService->createEvent($data, (int) $_SESSION['user_id']);
            $this->setFlash('success', 'Event created successfully!');
            $this->redirect('/events/' . $id);
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('events/create', [
            'pageTitle'  => 'Create Event',
            'sportTypes' => $sportTypes,
            'errors'     => $errors,
            'old'        => $data,
        ]);
    }

    public function show(string $id): void
    {
        $event = $this->eventService->getEvent((int) $id);
        if (!$event) {
            http_response_code(404);
            require ROOT_PATH . '/app/views/404.php';
            return;
        }

        $participants  = $this->eventService->getParticipants((int) $id);
        $approvedCount = $this->eventService->getApprovedCount((int) $id);

        $userStatus  = false;
        $isOrganizer = false;
        if (!empty($_SESSION['user_id'])) {
            $uid         = (int) $_SESSION['user_id'];
            $userStatus  = $this->eventService->getParticipantStatus((int) $id, $uid);
            $isOrganizer = ($event['organizer_id'] == $uid);
        }

        $vm = new EventViewModel($event, $participants, $approvedCount, $userStatus, $isOrganizer);

        $this->render('events/show', [
            'pageTitle' => $event['title'],
            'vm'        => $vm,
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $event = $this->eventService->getEvent((int) $id);

        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You do not have permission to edit this event.');
            $this->redirect('/events');
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('events/edit', [
            'pageTitle'  => 'Edit: ' . $event['title'],
            'event'      => $event,
            'sportTypes' => $sportTypes,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You do not have permission to update this event.');
            $this->redirect('/events');
        }

        $data   = $this->extractEventData();
        $errors = $this->eventService->validateEventData($data);

        if (empty($errors)) {
            $allowed = ['open', 'closed', 'cancelled'];
            $data['status'] = in_array($_POST['status'] ?? '', $allowed, true)
                ? $_POST['status']
                : $event['status'];

            $this->eventService->updateEvent((int) $id, $data);
            $this->setFlash('success', 'Event updated successfully!');
            $this->redirect('/events/' . $id);
        }

        $sportTypes = $this->eventService->getSportTypes();
        $this->render('events/edit', [
            'pageTitle'  => 'Edit: ' . $event['title'],
            'event'      => array_merge($event, $data),
            'sportTypes' => $sportTypes,
            'errors'     => $errors,
        ]);
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You do not have permission to cancel this event.');
            $this->redirect('/dashboard');
        }

        $this->eventService->cancelEvent((int) $id);

        $this->setFlash('success', 'Event "' . $event['title'] . '" has been cancelled.');
        $this->redirect('/dashboard');
    }

    public function join(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $eventId = (int) $id;
        $userId  = (int) $_SESSION['user_id'];

        try {
            $msg = $this->eventService->joinEvent($eventId, $userId, $_SESSION['username']);
        } catch (RuntimeException $e) {
            $this->setFlash('error', $e->getMessage());
            $this->redirect('/events/' . $id);
        }

        $this->setFlash('success', $msg);
        $this->redirect('/events/' . $id);
    }

    public function leave(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $eventId = (int) $id;
        $userId  = (int) $_SESSION['user_id'];

        $this->eventService->leaveEvent($eventId, $userId, $_SESSION['username']);

        $this->setFlash('success', 'You have left the event.');
        $this->redirect('/events/' . $id);
    }

    public function approve(string $id, string $userId): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            $this->redirect('/events/' . $id);
        }

        $this->eventService->approveParticipant((int) $id, (int) $userId);

        $this->setFlash('success', 'Participant approved.');
        $this->redirect('/events/' . $id);
    }

    public function reject(string $id, string $userId): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $event = $this->eventService->getEvent((int) $id);
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
            $this->redirect('/events/' . $id);
        }

        $this->eventService->rejectParticipant((int) $id, (int) $userId);

        $this->setFlash('success', 'Participant rejected.');
        $this->redirect('/events/' . $id);
    }

    private function extractEventData(): array
    {
        $allowed = ['beginner', 'intermediate', 'advanced'];
        $skill   = $_POST['skill_level'] ?? 'beginner';
        return [
            'title'             => trim($_POST['title']            ?? ''),
            'description'       => trim($_POST['description']      ?? ''),
            'sport_type_id'     => trim($_POST['sport_type_id']    ?? ''),
            'event_date'        => trim($_POST['event_date']       ?? ''),
            'location'          => trim($_POST['location']         ?? ''),
            'max_participants'  => trim($_POST['max_participants']  ?? ''),
            'requires_approval' => !empty($_POST['requires_approval']),
            'skill_level'       => in_array($skill, $allowed, true) ? $skill : 'beginner',
        ];
    }

}
