<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Global withdrawal request window.
 *
 * Withdrawal requests are only accepted between:
 *
 *     Friday 5:00 PM  ->  Sunday 11:59 PM (Monday 00:00)
 *
 * Every other moment — Monday 00:00 up to Friday 4:59 PM — is closed.
 * The window is a hard rule of the program, not a setting, so it is
 * enforced by this service in one place and every entry point
 * (member dashboard, Members table, Admin Users table, the shared
 * withdrawal modal) asks it the same question.
 */
class WithdrawalWindowService
{
    /** Friday, 5:00 PM — the first minute a request is accepted. */
    private const OPEN_DAY = CarbonInterface::FRIDAY;
    private const OPEN_HOUR = 17;
    private const OPEN_MINUTE = 0;

    /** Human label of the rule, used in messages. */
    public const SCHEDULE_LABEL = 'Friday 5:00 PM to Sunday 11:59 PM';

    /**
     * Monday — the only day a pending request may be approved or rejected.
     *
     * Approvals run from Monday 12:00 AM up to Monday 11:59:59 PM; every
     * other day the Approve / Reject actions are refused by the servers.
     */
    private const APPROVAL_DAY = CarbonInterface::MONDAY;

    /** Human label of the approval rule, used in messages. */
    public const APPROVAL_SCHEDULE_LABEL = 'Monday 12:00 AM to 11:59 PM';

    /**
     * Timezone the schedule is evaluated in.
     *
     * The window times are the members' local clock (Asia/Manila by
     * default, see config/withdrawal.php) — the server may well run on UTC.
     */
    public function timezone(): string
    {
        return (string) config('withdrawal.timezone', config('app.timezone', 'UTC'));
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now($this->timezone());
    }

    /**
     * Is a withdrawal request allowed right now?
     *
     * Friday is open only from 5:00 PM onwards; Saturday and Sunday are
     * open the whole day; Monday 12:00 AM closes the window again.
     */
    public function isOpen(?CarbonInterface $at = null): bool
    {
        $moment = $this->resolve($at);

        if (in_array($moment->dayOfWeek, [CarbonInterface::SATURDAY, CarbonInterface::SUNDAY], true)) {
            return true;
        }

        if ($moment->dayOfWeek !== self::OPEN_DAY) {
            return false;
        }

        return $moment->hour > self::OPEN_HOUR
            || ($moment->hour === self::OPEN_HOUR && $moment->minute >= self::OPEN_MINUTE);
    }

    /** When the window next opens (the given moment itself when already open). */
    public function opensAt(?CarbonInterface $at = null): CarbonImmutable
    {
        $moment = $this->resolve($at);

        if ($this->isOpen($moment)) {
            return $moment;
        }

        $daysUntilFriday = (self::OPEN_DAY - $moment->dayOfWeek + 7) % 7;

        $opening = $moment->startOfDay()
            ->addDays($daysUntilFriday)
            ->setTime(self::OPEN_HOUR, self::OPEN_MINUTE);

        return $opening->lessThanOrEqualTo($moment) ? $opening->addWeek() : $opening;
    }

    /** When the current (or next) open window closes: Monday 12:00 AM. */
    public function closesAt(?CarbonInterface $at = null): CarbonImmutable
    {
        $moment = $this->resolve($at);

        if (! $this->isOpen($moment)) {
            $moment = $this->opensAt($moment);
        }

        $daysUntilMonday = (CarbonInterface::MONDAY - $moment->dayOfWeek + 7) % 7;

        return $moment->startOfDay()->addDays($daysUntilMonday);
    }

    /**
     * Everything a view needs to render the state of the window.
     *
     * @return array{
     *     open: bool,
     *     message: string,
     *     opens_at: CarbonImmutable,
     *     closes_at: CarbonImmutable,
     *     opens_at_label: string,
     *     closes_at_label: string,
     *     schedule: string
     * }
     */
    public function status(?CarbonInterface $at = null): array
    {
        $moment = $this->resolve($at);
        $isOpen = $this->isOpen($moment);
        $opensAt = $this->opensAt($moment);
        $closesAt = $this->closesAt($moment);

        return [
            'open' => $isOpen,
            'message' => $this->message($moment),
            'opens_at' => $opensAt,
            'closes_at' => $closesAt,
            'opens_at_label' => $opensAt->format('l, F j, Y \a\t g:i A'),
            'closes_at_label' => $closesAt->format('l, F j, Y \a\t g:i A'),
            'schedule' => self::SCHEDULE_LABEL,
        ];
    }

    /** Short sentence explaining whether requests are accepted right now. */
    public function message(?CarbonInterface $at = null): string
    {
        $moment = $this->resolve($at);

        if ($this->isOpen($moment)) {
            return 'Withdrawal requests are open until '
                .$this->closesAt($moment)->subMinute()->format('l, F j, Y \a\t g:i A').'.';
        }

        return 'Withdrawal requests are only accepted from '.self::SCHEDULE_LABEL
            .'. The next window opens on '.$this->opensAt($moment)->format('l, F j, Y \a\t g:i A').'.';
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL WINDOW — Monday 12:00 AM to 11:59 PM only
    |--------------------------------------------------------------------------
    */

    /** May a pending withdrawal be approved or rejected right now? */
    public function isApprovalOpen(?CarbonInterface $at = null): bool
    {
        return $this->resolve($at)->dayOfWeek === self::APPROVAL_DAY;
    }

    /** When approvals next open: this Monday 12:00 AM, or the next one. */
    public function approvalOpensAt(?CarbonInterface $at = null): CarbonImmutable
    {
        $moment = $this->resolve($at);

        if ($this->isApprovalOpen($moment)) {
            return $moment->startOfDay();
        }

        $daysUntilMonday = (self::APPROVAL_DAY - $moment->dayOfWeek + 7) % 7;

        return $moment->startOfDay()->addDays($daysUntilMonday ?: 7);
    }

    /** When the current (or next) approval window closes: Tuesday 12:00 AM. */
    public function approvalClosesAt(?CarbonInterface $at = null): CarbonImmutable
    {
        return $this->approvalOpensAt($at)->addDay();
    }

    /** Short sentence explaining whether approve/reject is accepted right now. */
    public function approvalMessage(?CarbonInterface $at = null): string
    {
        $moment = $this->resolve($at);

        if ($this->isApprovalOpen($moment)) {
            return 'Withdrawal requests can be approved or rejected today until 11:59 PM.';
        }

        return 'Withdrawal requests can only be approved or rejected every '
            .self::APPROVAL_SCHEDULE_LABEL.'. The next approval window opens on '
            .$this->approvalOpensAt($moment)->format('l, F j, Y \\a\\t g:i A').'.';
    }

    /**
     * Everything a view needs to render the approval window state.
     *
     * @return array{
     *     open: bool,
     *     message: string,
     *     opens_at: CarbonImmutable,
     *     closes_at: CarbonImmutable,
     *     opens_at_label: string,
     *     closes_at_label: string,
     *     schedule: string
     * }
     */
    public function approvalStatus(?CarbonInterface $at = null): array
    {
        $moment = $this->resolve($at);
        $opensAt = $this->approvalOpensAt($moment);
        $closesAt = $this->approvalClosesAt($moment);

        return [
            'open' => $this->isApprovalOpen($moment),
            'message' => $this->approvalMessage($moment),
            'opens_at' => $opensAt,
            'closes_at' => $closesAt,
            'opens_at_label' => $opensAt->format('l, F j, Y \\a\\t g:i A'),
            'closes_at_label' => $closesAt->format('l, F j, Y \\a\\t g:i A'),
            'schedule' => self::APPROVAL_SCHEDULE_LABEL,
        ];
    }

    private function resolve(?CarbonInterface $at = null): CarbonImmutable
    {
        return $at
            ? CarbonImmutable::instance($at)->setTimezone($this->timezone())
            : $this->now();
    }
}
