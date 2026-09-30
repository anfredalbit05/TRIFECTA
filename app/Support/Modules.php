<?php

namespace App\Support;

use App\Models\Privilege;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Global module registry + permission checker.
 *
 * Single source of truth for the `modules` table IDs so no component
 * ever hardcodes a ModuleID again. Components (or any class) declare
 * which module they belong to and ask `can()` about actions.
 *
 * Matches the seeded modules/privileges rows:
 *   1  Masterlist - Admin Users
 *   2  Masterlist - Members
 *   3  Masterlist - Satellite Members
 *   4  Masterlist - Branches
 *   5  Masterlist - Benefits
 *   6  Masterlist - FAQs
 *   7  Masterlist - Inquiries
 *   8  Transaction List - Withdrawal
 *   9  Transaction List - Activations
 *   10 Post - News & Update
 *   11 Post - Testimonials
 *   12 Report & Settings - General
 *   13 Report & Settings - Users Reports
 *   14 Report & Settings - Ledgers
 *   15 Report & Settings - Pangkabuhayan
 */
final class Modules
{
    // Masterlist
    public const ADMIN_USERS = 1;

    public const MEMBERS = 2;

    public const SATELLITE_MEMBERS = 3;

    public const BRANCHES = 4;

    public const BENEFITS = 5;

    public const FAQS = 6;

    public const INQUIRIES = 7;

    // Transaction List
    public const TXN_WITHDRAWALS = 8;

    public const TXN_ACTIVATIONS = 9;

    // Post
    public const POST_NEWS = 10;

    public const POST_TESTIMONIALS = 11;

    // Report & Settings
    public const RS_GENERAL = 12;

    public const RS_USERS_REPORTS = 13;

    public const RS_LEDGERS = 14;

    public const RS_PANGKABUHAYAN = 15;

    /** Action => privileges column. */
    private const ACTIONS = [
        'create' => 'can_create',
        'read' => 'can_read',
        'update' => 'can_update',
        'delete' => 'can_delete',
        'print' => 'can_print',
        'change_status' => 'can_change_status',
    ];

    /**
     * Can the given user (defaults to the signed-in user) perform $action
     * on $moduleId? Actions: create|read|update|delete|print|change_status.
     */
    public static function can(int $moduleId, string $action, ?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        // Super Admin (RoleID 1) always has full access.
        if ((int) $user->RoleID === 1) {
            return true;
        }

        $column = self::ACTIONS[$action] ?? null;

        if ($column === null) {
            return false;
        }

        return (bool) self::privilege($user, $moduleId)?->{$column};
    }

    /** True when the user holds the ModuleID privilege row at all (any flag on). */
    public static function hasAny(int $moduleId, ?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user) {
            return false;
        }

        if ((int) $user->RoleID === 1) {
            return true;
        }

        return self::privilege($user, $moduleId) !== null;
    }

    /**
     * Privilege row for a user + module, or null. Queried fresh on every
     * check (no static cache) so permission changes saved from the User
     * Access Control matrix apply immediately, even under `php artisan serve`.
     */
    public static function privilege(User $user, int $moduleId): ?Privilege
    {
        return Privilege::query()
            ->where('RoleID', $user->RoleID)
            ->where('ModuleID', $moduleId)
            ->first();
    }
}
