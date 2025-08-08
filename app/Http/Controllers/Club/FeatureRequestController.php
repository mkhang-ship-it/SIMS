<?php
// Club FeatureRequestController - Updated to fix database schema mismatches
// Changes made: Fixed validation rules to match database enum values
// - Type: removed 'improvement' (only 'feature', 'bug' allowed)
// - Priority: removed 'critical' (only 'low', 'medium', 'high' allowed)
// - Status: fixed default value to match database schema

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeatureRequest;
use Illuminate\Support\Facades\Auth;

class FeatureRequestController extends Controller
{
    public function index()
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Get feature requests for this club
        $featureRequests = FeatureRequest::with(['createdBy'])
            ->where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.feature-requests.index', compact('featureRequests'));
    }

    public function store(Request $request)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:bug,feature',
            'priority' => 'required|string|in:low,medium,high',
        ]);

        $featureRequest = FeatureRequest::create([
            'club_id' => $clubId,
            'created_by' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => 'new_request',
        ]);

        return redirect()->route('backend.club.feature-requests.index')
            ->with('success', 'Yêu cầu tính năng đã được gửi thành công!');
    }
}
