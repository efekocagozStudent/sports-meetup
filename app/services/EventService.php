<?php
declare(strict_types=1);

class EventService implements IEventService
{
    private IEventRepository       $eventRepo;
    private IParticipantRepository $participantRepo;
    private INotificationService   $notifService;

    public function __construct()
    {
        $this->eventRepo       = new EventRepository();
        $this->participantRepo = new ParticipantRepository();
        $this->notifService    = new NotificationService();
    }

    public function listEvents(array $filters): array
    {
        return $this->eventRepo->all($filters);
    }

    public function getEvent(int $id): array|false
    {
        return $this->eventRepo->findById($id);
    }

    public function createEvent(array $data, int $organizerId): int
    {
        $data['organizer_id'] = $organizerId;
        return $this->eventRepo->create($data);
    }

    public function updateEvent(int $id, array $data): void
    {
        $this->eventRepo->update($id, $data);
    }

    public function cancelEvent(int $id): void
    {
        $event        = $this->eventRepo->findById($id);
        $participants = $this->participantRepo->getByEvent($id);

        $this->eventRepo->cancel($id);

        if ($event) {
            $this->notifService->notifyCancellation($participants, $event['title']);
        }
    }

    public function getOrganizerEvents(int $userId): array
    {
        return $this->eventRepo->getByOrganizer($userId);
    }

    /**
     * Join an event. Returns a flash message string, or throws on error.
     */
    public function joinEvent(int $eventId, int $userId, string $username): string
    {
        $event = $this->eventRepo->findById($eventId);

        if (!$event || $event['status'] !== 'open') {
            throw new RuntimeException('This event is not available for joining.');
        }
        if ($event['organizer_id'] == $userId) {
            throw new RuntimeException('You cannot join your own event as a participant.');
        }
        if ($this->participantRepo->getStatus($eventId, $userId) !== false) {
            throw new RuntimeException('You are already registered for this event.');
        }

        $approved = $this->participantRepo->countApproved($eventId);
        if ($approved >= $event['max_participants']) {
            throw new RuntimeException('Sorry, this event is full.');
        }

        $requiresApproval = (bool) $event['requires_approval'];
        $this->participantRepo->join($eventId, $userId, $requiresApproval);

        $this->notifService->notifyJoin(
            (int) $event['organizer_id'],
            $username,
            $event['title'],
            $eventId,
            $requiresApproval
        );

        return $requiresApproval
            ? 'Your join request has been sent to the organizer.'
            : 'You have successfully joined the event!';
    }

    public function leaveEvent(int $eventId, int $userId, string $username): void
    {
        $event = $this->eventRepo->findById($eventId);
        $this->participantRepo->leave($eventId, $userId);

        if ($event && $event['organizer_id'] != $userId) {
            $this->notifService->notifyLeave(
                (int) $event['organizer_id'],
                $username,
                $event['title'],
                $eventId
            );
        }
    }

    public function approveParticipant(int $eventId, int $userId): void
    {
        $this->participantRepo->updateStatus($eventId, $userId, 'approved');
        $event = $this->eventRepo->findById($eventId);
        if ($event) {
            $this->notifService->notifyApproval($userId, $event['title'], $eventId);
        }
    }

    public function rejectParticipant(int $eventId, int $userId): void
    {
        $this->participantRepo->updateStatus($eventId, $userId, 'rejected');
        $event = $this->eventRepo->findById($eventId);
        if ($event) {
            $this->notifService->notifyRejection($userId, $event['title'], $eventId);
        }
    }

    public function getParticipants(int $eventId): array
    {
        return $this->participantRepo->getByEvent($eventId);
    }

    public function getApprovedCount(int $eventId): int
    {
        return $this->participantRepo->countApproved($eventId);
    }

    public function getParticipantStatus(int $eventId, int $userId): string|false
    {
        return $this->participantRepo->getStatus($eventId, $userId);
    }

    public function getJoinedEvents(int $userId): array
    {
        return $this->participantRepo->getByUser($userId);
    }

    public function getPendingApprovals(int $organizerId): array
    {
        return $this->participantRepo->getPendingForOrganizer($organizerId);
    }

    public function getSportTypes(): array
    {
        return (new SportTypeRepository())->all();
    }

    public function getEventsForApi(): array
    {
        return $this->eventRepo->forApi();
    }

    public function allForAdmin(): array
    {
        return $this->eventRepo->allForAdmin();
    }

    public function adminDeleteEvent(int $id): void
    {
        $this->eventRepo->deleteById($id);
    }

    public function adminUpdateEvent(int $id, array $data): void
    {
        $this->eventRepo->update($id, $data);
    }

    public function validateEventData(array $data): array
    {
        $errors = [];

        $titleLen = strlen($data['title']);
        if ($titleLen < 3 || $titleLen > 100) {
            $errors[] = 'Title must be 3–100 characters.';
        }
        if (strlen($data['description']) < 10) {
            $errors[] = 'Description must be at least 10 characters.';
        }
        if (empty($data['sport_type_id']) || !ctype_digit((string) $data['sport_type_id'])) {
            $errors[] = 'Please select a sport type.';
        }
        if (empty($data['event_date'])) {
            $errors[] = 'Event date is required.';
        } else {
            try {
                $dt = new DateTimeImmutable($data['event_date']);
                if ($dt <= new DateTimeImmutable()) {
                    $errors[] = 'Event date must be in the future.';
                }
            } catch (Exception) {
                $errors[] = 'Invalid date or time format.';
            }
        }
        $locLen = strlen($data['location']);
        if ($locLen < 3 || $locLen > 255) {
            $errors[] = 'Location must be 3–255 characters.';
        }
        $max = (int) $data['max_participants'];
        if ($max < 2 || $max > 1000) {
            $errors[] = 'Max participants must be between 2 and 1000.';
        }

        return $errors;
    }
}
