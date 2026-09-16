<?php

namespace App\Services;

use App\Mail\SpinWheelWinner as SpinWheelWinnerMail;
use App\Models\NewsUpdate;
use App\Models\SpinDraw;
use App\Models\SpinWinner;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Spin the Wheel notifications.
 *
 *  - notifyWinner(): branded "you won" email with the prize and how to claim it.
 *  - announce(): one announcement post per cycle in the dashboard news feed,
 *    refreshed as winners are drawn (never one post per winner).
 *
 * Both are best effort — a mail or feed failure must never break a spin.
 */
class SpinWheelAnnouncer
{
    public const ANNOUNCEMENT_CATEGORY = 'Event';

    public const ANNOUNCEMENT_TITLE_PREFIX = '🎡 Spin the Wheel Winners';

    public function __construct(private SpinWheelService $wheel) {}

    /*
    |--------------------------------------------------------------------------
    | Winner email
    |--------------------------------------------------------------------------
    */

    /**
     * Email a winner their prize details and claim instructions.
     *
     * @return array{emailed: bool, email: ?string, message: string}
     */
    public function notifyWinner(SpinDraw $draw, SpinWinner $winner): array
    {
        $user = $winner->user;

        if (! $user) {
            return [
                'emailed' => false,
                'email' => null,
                'message' => 'The winning account no longer exists — please record the winner manually.',
            ];
        }

        $email = trim((string) $user->email);

        if ($email === '') {
            return [
                'emailed' => false,
                'email' => null,
                'message' => 'This member has no email on file — please inform '
                    .$this->wheel->displayName($user).' personally.',
            ];
        }

        $winNumber = SpinWinner::where('DrawID', $draw->DrawID)
            ->where('UserID', $winner->UserID)
            ->where('spin_order', '<=', $winner->spin_order)
            ->count();

        try {
            Mail::to($email)->send(new SpinWheelWinnerMail(
                $user,
                $draw,
                $winner,
                $this->wheel->claimInstructions(),
                max(1, $winNumber),
                $this->wheel->company(),
            ));

            Log::info('Spin the Wheel: winner email sent.', [
                'draw_date' => $draw->draw_date->toDateString(),
                'winner_id' => $winner->WinnerID,
                'user_id' => $winner->UserID,
                'email' => $email,
            ]);

            return [
                'emailed' => true,
                'email' => $email,
                'message' => 'Prize details emailed to '.$email.'.',
            ];
        } catch (Throwable $e) {
            Log::error('Spin the Wheel: winner email failed.', [
                'draw_date' => $draw->draw_date->toDateString(),
                'winner_id' => $winner->WinnerID,
                'user_id' => $winner->UserID,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'emailed' => false,
                'email' => $email,
                'message' => 'The prize email could not be sent to '.$email.'.',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | News feed announcement
    |--------------------------------------------------------------------------
    */

    public function announcementTitle(SpinDraw $draw): string
    {
        return self::ANNOUNCEMENT_TITLE_PREFIX.' — '.$draw->draw_date->format('F j, Y');
    }

    /**
     * Create or refresh the announcement post for a cycle. When a cycle has no
     * winners left (e.g. the last spin was undone) its post is removed.
     */
    public function announce(SpinDraw $draw, ?User $publisher = null): ?NewsUpdate
    {
        $draw->refresh();

        $winners = $this->wheel->winnersOf($draw);
        $existing = $draw->AnnouncementID
            ? NewsUpdate::find($draw->AnnouncementID)
            : NewsUpdate::where('title', $this->announcementTitle($draw))->first();

        if ($winners->isEmpty()) {
            if ($existing) {
                $existing->delete();
                $draw->forceFill(['AnnouncementID' => null])->save();
            }

            return null;
        }

        $title = $this->announcementTitle($draw);
        $details = $this->announcementBody($draw, $winners);

        try {
            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'details' => $details,
                    'category' => self::ANNOUNCEMENT_CATEGORY,
                    'status' => 'Published',
                ]);

                if ((int) $draw->AnnouncementID !== (int) $existing->ID) {
                    $draw->forceFill(['AnnouncementID' => $existing->ID])->save();
                }

                return $existing;
            }

            $post = NewsUpdate::create([
                'title' => $title,
                'details' => $details,
                'category' => self::ANNOUNCEMENT_CATEGORY,
                'image' => null,
                'status' => 'Published',
                'UserID' => $publisher?->UserID,
            ]);

            $draw->forceFill(['AnnouncementID' => $post->ID])->save();

            Log::info('Spin the Wheel: announcement posted.', [
                'draw_date' => $draw->draw_date->toDateString(),
                'news_update_id' => $post->ID,
            ]);

            return $post;
        } catch (Throwable $e) {
            Log::error('Spin the Wheel: announcement failed.', [
                'draw_date' => $draw->draw_date->toDateString(),
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  Collection<int, SpinWinner>  $winners
     */
    private function announcementBody(SpinDraw $draw, Collection $winners): string
    {
        $lines = [];

        $lines[] = '🎡 The Pangkabuhayan Spin the Wheel draw for '
            .$draw->draw_date->format('F j, Y').' is underway!';
        $lines[] = '';
        $lines[] = 'Pangkabuhayan collected: ₱'.number_format((float) $draw->pangkabuhayan_total, 2);
        $lines[] = 'Prize per winner: '.$draw->prize_name
            .' · ₱'.number_format((float) $draw->prize_amount, 2);
        $lines[] = 'Winners drawn: '.$winners->count().' of '.(int) $draw->winners_target;
        $lines[] = 'Covering invitations paid from '.$draw->period_start->format('M j, Y')
            .' to '.$draw->period_end->format('M j, Y').'.';
        $lines[] = '';
        $lines[] = '🏆 WINNERS';
        $lines[] = '';

        foreach ($winners as $winner) {
            $lines[] = $winner->spin_order.'. '
                .$this->wheel->displayName($winner->user)
                .' — '.$winner->prize_name
                .' (₱'.number_format((float) $winner->prize_amount, 2).')'
                .' · invited '.(int) $winner->qualified_invites.' member(s)';
        }

        $lines[] = '';

        if ($winners->count() < (int) $draw->winners_target) {
            $lines[] = '🎟️ '.((int) $draw->winners_target - $winners->count())
                .' winner slot(s) still to be drawn.';
            $lines[] = '';
        }

        $lines[] = 'Winners are notified by email. '.$this->wheel->claimInstructions();
        $lines[] = '';
        $lines[] = 'Congratulations to our winners — and thank you to everyone who invited new members this cycle!';

        return implode("\n", $lines);
    }
}
