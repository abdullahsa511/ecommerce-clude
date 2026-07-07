<?php

declare(strict_types=1);

namespace App\Core\Repositories\Showroom;

use DateTimeImmutable;
use DateTimeZone;
use JsonException;

/**
 * Australian showroom timezones: Sydney, Melbourne, Brisbane with offsets for the current instant.
 */
class ShowroomDateTimeRepository
{
    /** @var list<array{showroom_id: int, iana: string, city: string, label_suffix: string}> */
    private const ZONES = [
        ['showroom_id' => 1, 'iana' => 'Australia/Sydney', 'city' => 'Sydney', 'label_suffix' => 'Sydney time'],
        ['showroom_id' => 2, 'iana' => 'Australia/Melbourne', 'city' => 'Melbourne', 'label_suffix' => 'Melbourne time'],
        ['showroom_id' => 3, 'iana' => 'Australia/Brisbane', 'city' => 'Brisbane', 'label_suffix' => 'Brisbane time'],
    ];

    /**
     * @param \DateTimeImmutable|string|null $at Instant to evaluate in each showroom zone. Null uses "now".
     *
     * @return list<array{
     *     showroom_id: int,
     *     city: string,
     *     tz: string,
     *     date: string,
     *     local_time: string,
     *     offset_hours: int,
     *     gmt: string,
     *     abbr: string,
     *     isdst: bool,
     *     label: string
     * }>
     */
    public function getShowroomDateTimes(DateTimeImmutable|string|null $at = null): array
    {
        $out = [];
        foreach (self::ZONES as $row) {
            $iana = $row['iana'];
            $tz = new DateTimeZone($iana);
            $when = $this->resolveInstant($at, $tz);
            $offsetSeconds = $tz->getOffset($when);
            $offsetHours = (int) ($offsetSeconds / 3600);
            $sign = $offsetHours >= 0 ? '+' : '';
            $gmtLabel = sprintf('GMT%s%d:00', $sign, abs($offsetHours));
            $isDst = (bool) (int) $when->format('I');
            $isBrisbane = $iana === 'Australia/Brisbane';
            // QLD: no DST — always AEST. NSW/VIC: AEDT while DST active, else AEST.
            $abbr = $isBrisbane ? 'AEST' : ($isDst ? 'AEST/ AEDT' : 'AEST/ AEDT');
            $label = sprintf('%s (%s) %s', $abbr, $gmtLabel, $row['label_suffix']);

            $out[] = [
                'showroom_id' => $row['showroom_id'],
                'city' => $row['city'],
                'tz' => $iana,
                'date' => $when->format('Y-m-d'),
                'local_time' => $when->format('Y-m-d H:i:s'),
                'offset_hours' => $offsetHours,
                'gmt' => $gmtLabel,
                'abbr' => $abbr,
                'isdst' => $isDst,
                'label' => $label,
            ];
        }

        return $out;
    }

    /**
     * JSON array for API responses (current date/time per showroom zone).
     *
     * @param \DateTimeImmutable|string|null $at Optional instant for tests (e.g. '2026-01-11 12:00:00').
     *
     * @throws JsonException
     */
    public function getShowroomDateTimesJson(
        int $flags = JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        DateTimeImmutable|string|null $at = null
    ): string {
        return json_encode($this->getShowroomDateTimes($at), $flags);
    }

    private function resolveInstant(DateTimeImmutable|string|null $at, DateTimeZone $tz): DateTimeImmutable
    {
        if ($at === null) {
            return new DateTimeImmutable('now', $tz);
        }

        if ($at instanceof DateTimeImmutable) {
            return $at->setTimezone($tz);
        }

        return new DateTimeImmutable($at, $tz);
    }
}
