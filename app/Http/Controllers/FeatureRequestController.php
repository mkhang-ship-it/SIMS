<?php
// FeatureRequestController - Updated to match database structure
// Fixed validation rules and status values according to migration schema
// Handles CRUD operations for feature requests with modal-based interface
// Features: View, update, and delete feature requests
// No create functionality as requested by user

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeatureRequest;
use App\Models\Club;
use App\Models\User;

class FeatureRequestController extends Controller
{
    public function index()
    {
        // Get all feature requests with relationships
        $featureRequests = FeatureRequest::with(['club', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all clubs for filtering
        $clubs = Club::where('status', 'active')->orderBy('name')->get();

        return view('feature-requests.index', compact('featureRequests', 'clubs'));
    }

    public function update(Request $request, FeatureRequest $featureRequest)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:feature,bug',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:new_request,acknowledged,in_progress,completed,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'priority' => $request->priority,
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
        ];

        // Update timestamps based on status changes
        if ($request->status === 'acknowledged' && $featureRequest->status !== 'acknowledged') {
            $updateData['acknowledged_at'] = now();
        }

        if ($request->status === 'in_progress' && $featureRequest->status !== 'in_progress') {
            $updateData['started_at'] = now();
        }

        if ($request->status === 'completed' && $featureRequest->status !== 'completed') {
            $updateData['completed_at'] = now();
        }

        $featureRequest->update($updateData);

        return redirect()->route('backend.feature-requests.index')->with('success', 'Yêu cầu tính năng đã được cập nhật thành công!');
    }

    public function destroy(FeatureRequest $featureRequest)
    {
        $featureRequest->delete();

        return redirect()->route('backend.feature-requests.index')->with('success', 'Yêu cầu tính năng đã được xóa thành công!');
    }
}
