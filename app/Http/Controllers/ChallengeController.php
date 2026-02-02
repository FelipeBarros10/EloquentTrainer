<?php

namespace App\Http\Controllers;

use App\Challenges\ChallengeRepository;
use App\Models\ChallengeProgress;
use App\Services\EloquentJudgeService;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $challenges = ChallengeRepository::all();
        $user = $request->user();

        $progress = ChallengeProgress::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('challenge_id');

        $completed = $progress
            ->filter(fn (ChallengeProgress $p) => $p->completed)
            ->mapWithKeys(fn (ChallengeProgress $p) => [$p->challenge_id => true])
            ->toArray();

        $score = (int) $progress->sum('points_awarded');

        return view('challenges.index', [
            'challenges' => $challenges,
            'completed' => $completed,
            'score' => $score,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $challenge = ChallengeRepository::findOrFail($id);
        $user = $request->user();

        $progress = ChallengeProgress::query()
            ->where('user_id', $user->id)
            ->where('challenge_id', $id)
            ->first();

        $completed = $progress && $progress->completed ? [$id => true] : [];
        $score = (int) ChallengeProgress::query()->where('user_id', $user->id)->sum('points_awarded');
        $code = (string) ($progress?->last_code ?? $challenge['starter_code']);

        return view('challenges.show', [
            'challenge' => $challenge,
            'completed' => $completed,
            'score' => $score,
            'code' => $code,
            'result' => null,
        ]);
    }

    public function submit(Request $request, string $id, EloquentJudgeService $judge)
    {
        $challenge = ChallengeRepository::findOrFail($id);
        $user = $request->user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:4000'],
        ]);

        $code = $validated['code'];

        $result = $judge->evaluate($code, $challenge['gold']);

        $progress = ChallengeProgress::query()->firstOrCreate(
            ['user_id' => $user->id, 'challenge_id' => $id],
            ['completed' => false, 'points_awarded' => 0, 'attempts' => 0]
        );

        $progress->attempts = (int) $progress->attempts + 1;
        $progress->last_code = $code;

        if ($result['passed'] && !$progress->completed) {
            $progress->completed = true;
            $progress->points_awarded = (int) $challenge['points'];
            $progress->completed_at = now();
        }

        $progress->save();

        $completed = $progress->completed ? [$id => true] : [];
        $score = (int) ChallengeProgress::query()->where('user_id', $user->id)->sum('points_awarded');

        return view('challenges.show', [
            'challenge' => $challenge,
            'completed' => $completed,
            'score' => $score,
            'code' => $code,
            'result' => $result,
        ]);
    }

    public function reset(Request $request, string $id)
    {
        $user = $request->user();

        ChallengeProgress::query()
            ->where('user_id', $user->id)
            ->where('challenge_id', $id)
            ->update(['last_code' => null]);

        return redirect()->route('challenges.show', ['id' => $id]);
    }
}
