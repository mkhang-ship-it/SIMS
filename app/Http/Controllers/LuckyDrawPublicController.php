<?php
// Updated: Added prize availability checks and participant winner validation for lucky draw system
// - Modified getPrizes() to only return available prizes with remaining quantity
// - Added checkParticipantEligibility() method to validate if participant can win
// - Added prize availability validation in saveSpinResult()

namespace App\Http\Controllers;

use App\Models\LuckyDraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LuckyDrawPublicController extends Controller
{
    // Fixed: Replaced call to undefined method 'values()' on Eloquent relation with 'get()' to retrieve a collection.
    public function show($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        $prizes = $luckyDraw->prizes()->orderBy('order')->get();
        $total = $prizes->count();

        // Tách thành 2 nhóm: tối đa 3 ảnh ở hàng 1, còn lại ở hàng 2
        $prizesChunk1 = $prizes->slice(0, 3);
        $prizesChunk2 = $prizes->slice(3);

        // Check if lucky draw is active and not expired
        if (!$luckyDraw->isActive()) {
            abort(404, 'Lucky draw not found or has expired.');
        }

        return view('lucky-draw.show', compact('luckyDraw', 'prizesChunk1', 'prizesChunk2'));
    }

    public function getPrizes($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        // Get only available prizes (those with remaining quantity > 0)
        $prizes = [];
        $prizeList = $luckyDraw->prizes()->orderBy('order')->get();

        foreach ($prizeList as $index => $prize) {
            if ($prize->isAvailable()) {
                $prizes[$index + 1] = $prize->name;
            }
        }

        return response()->json($prizes);
    }

    public function getPrizesWithDetails($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        // Get all prizes with their availability information
        $prizes = [];
        $prizeList = $luckyDraw->prizes()->orderBy('order')->get();

        foreach ($prizeList as $index => $prize) {
            $prizes[] = [
                'id' => $index + 1,
                'name' => $prize->name,
                'quantity' => $prize->quantity,
                'remaining_quantity' => $prize->remaining_quantity,
                'won_count' => $prize->won_count,
                'is_available' => $prize->isAvailable(),
                'special_gift' => $prize->special_gift
            ];
        }

        return response()->json($prizes);
    }

    public function getParticipants($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        // Return participants as a simple array instead of key-value object
        $participants = $luckyDraw->participants ?? [];
        return response()->json(array_values($participants));
    }

    public function getWinners($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        // Return winners as they are stored, ensuring proper structure
        $winners = $luckyDraw->winners ?? [];
        return response()->json($winners);
    }

    public function checkParticipantEligibility($slug, Request $request)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $participantName = $request->input('participant');
        $prizeName = $request->input('prize');

        // Check if participant has already won
        $winners = $luckyDraw->winners ?? [];
        $hasWon = collect($winners)->contains('name', $participantName);

        if ($hasWon) {
            return response()->json([
                'eligible' => false,
                'reason' => 'Người tham dự đã trúng giải trước đó'
            ]);
        }

        // Check if prize is available
        if ($prizeName) {
            $prize = $luckyDraw->prizes()->where('name', $prizeName)->first();
            if (!$prize || !$prize->isAvailable()) {
                return response()->json([
                    'eligible' => false,
                    'reason' => 'Giải thưởng đã hết hoặc không tồn tại'
                ]);
            }
        }

        return response()->json([
            'eligible' => true,
            'reason' => 'Có thể tham gia quay thưởng'
        ]);
    }

    public function savePrizes(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $prizeSetting = $request->input('prizeSetting', '');
        $lines = explode("\n", trim($prizeSetting));

        // Clear existing prizes
        $luckyDraw->prizes()->delete();

        // Add new prizes
        foreach ($lines as $index => $line) {
            if (!empty(trim($line))) {
                $luckyDraw->prizes()->create([
                    'name' => trim($line),
                    'order' => $index + 1,
                    'quantity' => 1, // Default quantity
                    'special_gift' => false // Default to false
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Prizes updated successfully']);
    }

    public function saveParticipants(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $participants = $request->input('participants', '');
        $lines = explode("\n", trim($participants));
        $participantList = [];

        foreach ($lines as $index => $line) {
            if (!empty(trim($line))) {
                $participantList[$index + 1] = trim($line);
            }
        }

        $luckyDraw->update(['participants' => $participantList]);

        return response()->json(['success' => true, 'message' => 'Participants updated successfully']);
    }

    public function saveSpinResult(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $result = $request->input('result');

        if (!empty($result)) {
            $resultData = json_decode($result, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $participantName = $resultData['name'] ?? '';
                $prizeName = $resultData['prize'] ?? $resultData['price'] ?? '';

                // Validate required fields
                if (empty($participantName)) {
                    return response()->json([
                        'error' => 'Tên người tham dự không được để trống',
                        'success' => false
                    ], 400);
                }

                // Validate participant eligibility
                $winners = $luckyDraw->winners ?? [];
                $hasWon = collect($winners)->contains('name', $participantName);

                if ($hasWon) {
                    return response()->json([
                        'error' => 'Người tham dự đã trúng giải trước đó',
                        'success' => false
                    ], 400);
                }

                // Validate prize availability
                if ($prizeName) {
                    $prize = $luckyDraw->prizes()->where('name', $prizeName)->first();
                    if (!$prize) {
                        return response()->json([
                            'error' => 'Giải thưởng không tồn tại',
                            'success' => false
                        ], 400);
                    }

                    if (!$prize->isAvailable()) {
                        return response()->json([
                            'error' => 'Giải thưởng đã hết',
                            'success' => false
                        ], 400);
                    }
                }

                $resultData['won_at'] = now()->toDateTimeString();
                $luckyDraw->addWinner($participantName, $prizeName);

                return response()->json(['success' => true, 'message' => 'Result saved successfully']);
            } else {
                return response()->json(['error' => 'Invalid JSON format'], 400);
            }
        }

        return response()->json(['error' => 'No result data'], 400);
    }

    public function clearWinners(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $luckyDraw->clearWinners();

        return response()->json(['success' => true, 'message' => 'Winners list cleared']);
    }

    public function handleApi(Request $request, $slug)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'savePrizeSetting':
                    return $this->savePrizes($request, $slug);

                case 'saveParticipants':
                    return $this->saveParticipants($request, $slug);

                case 'saveSpinResult':
                    return $this->saveSpinResult($request, $slug);

                case 'clearWinners':
                    return $this->clearWinners($request, $slug);

                default:
                    return response()->json(['error' => 'Invalid action'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Lucky Draw API Error: ' . $e->getMessage(), [
                'slug' => $slug,
                'action' => $request->input('action'),
                'request' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
