<?php

namespace App\Http\Controllers;

use App\Services\ProgressionService;
use Illuminate\Support\Facades\Auth;

class ProgressionController extends Controller
{
    public function __construct(
        private readonly ProgressionService $progressionService
    ) {}

    public function __invoke()
    {
        $user = Auth::user();
        $data = $this->progressionService->getProgression($user->id);

        // Calculate current level XP progress
        $xpInCurrentLevel = $data['totalXp'] - $data['xpForCurrentLevel'];
        $levelProgress = $data['xpForNextLevel'] > 0
            ? min(100, max(0, ($xpInCurrentLevel / $data['xpForNextLevel']) * 100))
            : 0;
        $data['levelProgress'] = (int) round($levelProgress);

        return view('progression.index', $data);
    }
}
