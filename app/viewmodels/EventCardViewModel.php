<?php
declare(strict_types=1);


class EventCardViewModel
{
    public int    $id;
    public string $title;
    public string $location;
    public string $eventDate;
    public string $organizerName;
    public string $sportName;
    public string $sportIcon;
    public int    $sportTypeId;
    public string $status;
    public string $skillLevel;
    public int    $maxParticipants;
    public int    $participantCount;
    public bool   $requiresApproval;

    // ── Computed display values ───────────────────────────────────────────

    public int    $spotsLeft;
    public int    $fillPercent;
    public string $fillClass;   // '', 'almost-full', or 'full'

    public function __construct(array $row)
    {
        $this->id               = (int)  $row['id'];
        $this->title            =        $row['title'];
        $this->location         =        $row['location'];
        $this->eventDate        =        $row['event_date'];
        $this->organizerName    =        $row['organizer_name']   ?? '';
        $this->sportName        =        $row['sport_name']       ?? '';
        $this->sportIcon        =        $row['sport_icon']       ?? '';
        $this->sportTypeId      = (int)  $row['sport_type_id'];
        $this->status           =        $row['status'];
        $this->skillLevel       =        $row['skill_level']      ?? '';
        $this->maxParticipants  = (int)  $row['max_participants'];
        $this->participantCount = (int) ($row['participant_count'] ?? 0);
        $this->requiresApproval = (bool) $row['requires_approval'];

        $this->spotsLeft   = max(0, $this->maxParticipants - $this->participantCount);
        $this->fillPercent = $this->maxParticipants > 0
            ? (int) round(($this->participantCount / $this->maxParticipants) * 100)
            : 0;
        $this->fillClass = match (true) {
            $this->fillPercent >= 100 => 'full',
            $this->fillPercent >= 75  => 'almost-full',
            default                   => '',
        };
    }

    /** Build an array of EventCardViewModels from a raw rows array. */
    public static function fromRows(array $rows): array
    {
        return array_map(static fn(array $row) => new self($row), $rows);
    }
}
