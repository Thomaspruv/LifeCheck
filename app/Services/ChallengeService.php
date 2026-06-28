<?php

namespace App\Services;

use App\Models\PersonalChallenge;
use App\Models\ChallengeProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengeService
{
    /**
     * Get all challenges for the authenticated user, ordered by creation date.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserChallenges()
    {
        return PersonalChallenge::where('user_id', Auth::id())
            ->withCount(['progress as done_days' => fn ($q) => $q->where('is_done', true)])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get challenges filtered by status.
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChallengesByStatus(string $status)
    {
        return PersonalChallenge::where('user_id', Auth::id())
            ->where('status', $status)
            ->withCount(['progress as done_days' => fn ($q) => $q->where('is_done', true)])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new personal challenge.
     *
     * @param array $data
     * @return PersonalChallenge
     */
    public function createChallenge(array $data): PersonalChallenge
    {
        return PersonalChallenge::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration_days' => (int) ($data['duration_days'] ?? 7),
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    /**
     * Log progress for a challenge on a given date.
     *
     * @param PersonalChallenge $challenge
     * @param string|null $date
     * @param bool $isDone
     * @param string|null $note
     * @return ChallengeProgress
     */
    public function logProgress(PersonalChallenge $challenge, ?string $date = null, bool $isDone = true, ?string $note = null): ChallengeProgress
    {
        $date = $date ?? now()->toDateString();

        $progress = ChallengeProgress::updateOrCreate(
            [
                'personal_challenge_id' => $challenge->id,
                'date' => $date,
            ],
            [
                'is_done' => $isDone,
                'note' => $note,
            ]
        );

        $this->checkCompletion($challenge);

        return $progress;
    }

    /**
     * Check if the challenge should be marked as completed based on progress.
     *
     * @param PersonalChallenge $challenge
     * @return void
     */
    public function checkCompletion(PersonalChallenge $challenge): void
    {
        if ($challenge->status !== 'active') {
            return;
        }

        $doneDays = ChallengeProgress::where('personal_challenge_id', $challenge->id)
            ->where('is_done', true)
            ->count();

        if ($doneDays >= $challenge->duration_days) {
            $challenge->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Pause an active challenge.
     *
     * @param PersonalChallenge $challenge
     * @return void
     */
    public function pauseChallenge(PersonalChallenge $challenge): void
    {
        if ($challenge->status === 'active') {
            $challenge->update(['status' => 'paused']);
        }
    }

    /**
     * Resume a paused challenge.
     *
     * @param PersonalChallenge $challenge
     * @return void
     */
    public function resumeChallenge(PersonalChallenge $challenge): void
    {
        if ($challenge->status === 'paused') {
            $challenge->update(['status' => 'active']);
        }
    }

    /**
     * Mark a challenge as failed.
     *
     * @param PersonalChallenge $challenge
     * @return void
     */
    public function failChallenge(PersonalChallenge $challenge): void
    {
        if (in_array($challenge->status, ['active', 'paused'])) {
            $challenge->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Delete a challenge and its progress.
     *
     * @param PersonalChallenge $challenge
     * @return void
     */
    public function deleteChallenge(PersonalChallenge $challenge): void
    {
        $challenge->delete();
    }

    /**
     * Get the calendar data for a challenge in a given month.
     *
     * @param PersonalChallenge $challenge
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getCalendarData(PersonalChallenge $challenge, int $year, int $month): array
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateString();

        $progressRecords = ChallengeProgress::where('personal_challenge_id', $challenge->id)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($p) => $p->date instanceof \Carbon\Carbon ? $p->date->toDateString() : (string) $p->date);

        $today = now()->toDateString();
        $daysInMonth = \Carbon\Carbon::parse($start)->daysInMonth;
        $calendar = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $record = $progressRecords->get($dateStr);

            $calendar[] = [
                'day' => $day,
                'date' => $dateStr,
                'isDone' => $record ? $record->is_done : false,
                'hasEntry' => $record !== null,
                'isToday' => $dateStr === $today,
                'isFuture' => $dateStr > $today,
            ];
        }

        return $calendar;
    }
}
