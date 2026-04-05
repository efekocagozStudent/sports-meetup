<?php
declare(strict_types=1);

class ApiController extends Controller
{
    private IEventService        $eventService;
    private ISportTypeRepository $sportTypeRepo;

    public function __construct()
    {
        $this->eventService  = new EventService();
        $this->sportTypeRepo = new SportTypeRepository();
    }

   public function events(): void
    {
        $events = $this->eventService->getEventsForApi();
        $sport    = strtolower(trim($_GET['sport']    ?? ''));
        $search   = strtolower(trim($_GET['search']   ?? ''));
        $status   = strtolower(trim($_GET['status']   ?? ''));
        $upcoming = !empty($_GET['upcoming']);

        // Filtering (case-insensitive)
        if ($sport !== '') {            $events = array_values(array_filter(
                $events, fn($e) => strtolower($e['sport']) === $sport
            ));
        }
        if ($search !== '') {
            $events = array_values(array_filter(
                $events,
                fn($e) => str_contains(strtolower($e['title']), $search)
                       || str_contains(strtolower($e['location']), $search)
            ));
        }
        if ($status !== '') {
            $events = array_values(array_filter(
                $events, fn($e) => $e['status'] === $status
            ));
        }
        if ($upcoming) {
            $now    = new DateTimeImmutable();
            $events = array_values(array_filter(
                $events, fn($e) => new DateTimeImmutable($e['event_date']) > $now
            ));
        }

        $this->json([
            'success' => true,
            'count'   => count($events),
            'data'    => $events,
        ]);
    }

    public function event(string $id): void
    {
        if (!ctype_digit($id)) {
            $this->json(['success' => false, 'message' => 'Invalid event ID.'], 400);
        }

        $event = $this->eventService->getEvent((int) $id);
        if (!$event || $event['status'] === 'cancelled') {
            $this->json(['success' => false, 'message' => 'Event not found.'], 404);
        }

        $this->json(['success' => true, 'data' => [
            'id'               => $event['id'],
            'title'            => $event['title'],
            'description'      => $event['description'],
            'sport'            => $event['sport_name'],
            'sport_icon'       => $event['sport_icon']      ?? '',
            'skill_level'      => $event['skill_level']     ?? '',
            'organizer'        => $event['organizer_name'],
            'event_date'       => $event['event_date'],
            'location'         => $event['location'],
            'max_participants' => $event['max_participants'],
            'requires_approval'=> (bool) $event['requires_approval'],
            'status'           => $event['status'],
        ]]);
    }

    public function sports(): void
    {
        $types = $this->sportTypeRepo->all();
        $this->json([
            'success' => true,
            'data'    => $types,
        ]);
    }
}
