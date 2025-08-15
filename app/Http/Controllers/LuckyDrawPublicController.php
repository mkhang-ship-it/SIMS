<?php

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

        // Check if lucky draw is active and not expired
        if (!$luckyDraw->isActive()) {
            abort(404, 'Lucky draw not found or has expired.');
        }

        $prizes = $luckyDraw->prizes()->orderBy('order')->get();
        $total = $prizes->count();

        // Tách thành 2 nhóm: tối đa 3 ảnh ở hàng 1, còn lại ở hàng 2
        $prizesChunk1 = $prizes->slice(0, 3);
        $prizesChunk2 = $prizes->slice(3);

        return view('public.homepage.index', compact('luckyDraw', 'prizesChunk1', 'prizesChunk2'));
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

        // Check security verification
        if (!$this->checkSecurityVerification($slug)) {
            return response()->json(['error' => 'Mã bảo mật không đúng'], 403);
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

        // Check security verification
        if (!$this->checkSecurityVerification($slug)) {
            return response()->json(['error' => 'Mã bảo mật không đúng'], 403);
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

        // Check security verification
        if (!$this->checkSecurityVerification($slug)) {
            return response()->json(['error' => 'Mã bảo mật không đúng'], 403);
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

                // Validate participant eligibility (only for real prizes, not for no-prize mode)
                if ($prizeName && $prizeName !== '-' && $prizeName !== 'Quay số may mắn') {
                    $winners = $luckyDraw->winners ?? [];
                    $hasWon = collect($winners)->contains('name', $participantName);

                    if ($hasWon) {
                        return response()->json([
                            'error' => 'Người tham dự đã trúng giải trước đó',
                            'success' => false
                        ], 400);
                    }
                }

                // Validate prize availability (only if it's a real prize, not no-prize mode)
                if ($prizeName && $prizeName !== '-' && $prizeName !== 'Quay số may mắn') {
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

        // Check security verification
        if (!$this->checkSecurityVerification($slug)) {
            return response()->json(['error' => 'Mã bảo mật không đúng'], 403);
        }

        $luckyDraw->clearWinners();

        return response()->json(['success' => true, 'message' => 'Winners list cleared']);
    }

    public function handleApi(Request $request, $slug)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'verifySecurityCode':
                    return $this->verifySecurityCode($request, $slug);

                case 'checkSecurityStatus':
                    return $this->checkSecurityStatus($request, $slug);

                case 'resetSecurityVerification':
                    return $this->resetSecurityVerification($request, $slug);

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

    public function verifySecurityCode(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $securityCode = $request->input('security_code');

        if ($securityCode === $luckyDraw->security_code) {
            // Store verification in session for 60 minutes
            session([
                'lucky_draw_verified_' . $slug => [
                    'verified' => true,
                    'expires_at' => now()->addMinutes(60)->timestamp
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security code verified successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Mã bảo mật không đúng'
            ], 403);
        }
    }

    private function checkSecurityVerification($slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        // If no security code is required, allow access
        if (!$luckyDraw->security_code) {
            return true;
        }

        // Check if user is verified in session and not expired
        $verification = session('lucky_draw_verified_' . $slug);

        if (!$verification) {
            return false;
        }

        // Check if verification is expired
        if (isset($verification['expires_at']) && $verification['expires_at'] < now()->timestamp) {
            // Remove expired verification
            session()->forget('lucky_draw_verified_' . $slug);
            return false;
        }

        return $verification['verified'] ?? false;
    }

    public function checkSecurityStatus(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        $isVerified = $this->checkSecurityVerification($slug);

        return response()->json([
            'verified' => $isVerified
        ]);
    }

    public function resetSecurityVerification(Request $request, $slug)
    {
        $luckyDraw = LuckyDraw::where('slug', $slug)->firstOrFail();

        if (!$luckyDraw->isActive()) {
            return response()->json(['error' => 'Lucky draw not active'], 404);
        }

        // Remove verification from session
        session()->forget('lucky_draw_verified_' . $slug);

        return response()->json([
            'success' => true,
            'message' => 'Security verification reset'
        ]);
    }
}
