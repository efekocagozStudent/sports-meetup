<?php
declare(strict_types=1);

class NotificationService implements INotificationService
{
    private INotificationRepository $notifRepo;

    public function __construct()
    {
        $this->notifRepo = new NotificationRepository();
    }

    public function getForUser(int $userId): array
    {
        return $this->notifRepo->getForUser($userId, 50);
    }

    public function countUnread(int $userId): int
    {
        return $this->notifRepo->countUnread($userId);
    }

    public function markAllRead(int $userId): void
    {
        $this->notifRepo->markAllRead($userId);
    }

    public function deleteAll(int $userId): void
    {
        $this->notifRepo->deleteAll($userId);
    }

    public function notifyJoin(int $organizerId, string $username, string $eventTitle, int $eventId, bool $requiresApproval): void
    {
        if ($requiresApproval) {
            $this->notifRepo->create(
                $organizerId,
                'join_request',
                '🙋 ' . $username . ' requested to join "' . $eventTitle . '".',
                '/events/' . $eventId
            );
        } else {
            $this->notifRepo->create(
                $organizerId,
                'participant_joined',
                '✅ ' . $username . ' joined your event "' . $eventTitle . '".',
                '/events/' . $eventId
            );
        }
    }

    public function notifyLeave(int $organizerId, string $username, string $eventTitle, int $eventId): void
    {
        $this->notifRepo->create(
            $organizerId,
            'participant_left',
            '👋 ' . $username . ' left your event "' . $eventTitle . '".',
            '/events/' . $eventId
        );
    }

    public function notifyApproval(int $userId, string $eventTitle, int $eventId): void
    {
        $this->notifRepo->create(
            $userId,
            'request_approved',
            '🎉 Your request to join "' . $eventTitle . '" was approved!',
            '/events/' . $eventId
        );
    }

    public function notifyRejection(int $userId, string $eventTitle, int $eventId): void
    {
        $this->notifRepo->create(
            $userId,
            'request_rejected',
            '❌ Your request to join "' . $eventTitle . '" was declined.',
            '/events/' . $eventId
        );
    }

    public function notifyCancellation(array $participants, string $eventTitle): void
    {
        foreach ($participants as $p) {
            $this->notifRepo->create(
                (int) $p['user_id'],
                'event_cancelled',
                '❌ "' . $eventTitle . '" has been cancelled by the organiser.',
                '/events'
            );
        }
    }
}
