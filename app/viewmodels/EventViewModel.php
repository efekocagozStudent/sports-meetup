<?php
declare(strict_types=1);


class EventViewModel
{
    public array        $event;
    public array        $participants;
    public int          $approvedCount;
    public int          $fillPercent;
    public string       $fillClass;
    public string|false $userStatus;    // null-safe: false when guest
    public bool         $isOrganizer;

   
    public int    $spotsLeft;

    public function __construct(
        array        $event,
        array        $participants,
        int          $approvedCount,
        string|false $userStatus  = false,
        bool         $isOrganizer = false,
    ) {
        $this->event         = $event;
        $this->participants  = $participants;
        $this->approvedCount = $approvedCount;
        $this->userStatus    = $userStatus;
        $this->isOrganizer   = $isOrganizer;

        $max = (int) $event['max_participants'];
        $this->spotsLeft   = max(0, $max - $approvedCount);
        $this->fillPercent = $max > 0
            ? (int) round(($approvedCount / $max) * 100)
            : 0;
        $this->fillClass = match (true) {
            $this->fillPercent >= 100 => 'full',
            $this->fillPercent >= 75  => 'almost-full',
            default                   => '',
        };
    }
}
