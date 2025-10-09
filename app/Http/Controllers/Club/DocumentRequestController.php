<?php

/**
 * DocumentRequestController - Quản lý yêu cầu nộp tài liệu cho hệ thống multi-club
 *
 * THAY ĐỔI MỚI NHẤT:
 * - Loại bỏ trường 'title' không có trong DB
 * - Loại bỏ trường 'status' trong DocumentRequestUser, dùng document_id để xác định trạng thái
 * - Sửa fillable trong DocumentRequest model để thêm club_id
 * - Cập nhật tên relationships cho phù hợp
 *
 * TÍNH NĂNG:
 * - Admin tạo yêu cầu nộp tài liệu theo loại văn bản
 * - Chọn users cần nộp tài liệu với deadline
 * - Theo dõi tiến độ nộp tài liệu của từng user (null document_id = pending, có document_id = completed)
 * - Hỗ trợ phân tách dữ liệu theo từng CLB (club_id)
 */

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestUser;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\UserClubRole;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    /**
     * Display a listing of document requests for current club
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $query = DocumentRequest::with(['documentType', 'requestedByUser'])
            ->where('club_id', $clubId);

        // Filter by document type if provided
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        // Filter by status (based on deadline)
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->where('deadline', '<', now());
            } elseif ($request->status === 'active') {
                $query->where('deadline', '>=', now());
            }
        }

        $documentRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get document types for filter
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('clubs.document-requests.index', compact('documentRequests', 'documentTypes'));
    }

    /**
     * Show the form for creating a new document request
     */
    public function create()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get active document types
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get club members
        $clubMembers = User::whereHas('userClubRoles', function ($query) use ($clubId) {
            $query->where('club_id', $clubId)
                ->where('status', 'active');
        })->orderBy('name')->get();

        return view('clubs.document-requests.create', compact('documentTypes', 'clubMembers'));
    }

    /**
     * Store a newly created document request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'deadline' => 'required|date|after:today',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Verify document type belongs to club
        $documentType = DocumentType::where('id', $validatedData['document_type_id'])
            ->where('club_id', $clubId)
            ->first();

        if (!$documentType) {
            return redirect()->back()->withInput()
                ->with('error', 'Loại văn bản không hợp lệ!');
        }

        try {
            // Create document request
            $documentRequest = DocumentRequest::create([
                'club_id' => $clubId,
                'document_type_id' => $validatedData['document_type_id'],
                'requested_by' => $user->id,
                'deadline' => $validatedData['deadline']
            ]);

            // Create document request users
            foreach ($validatedData['user_ids'] as $userId) {
                // Verify user belongs to club
                $userClubRole = UserClubRole::where('user_id', $userId)
                    ->where('club_id', $clubId)
                    ->first();

                if ($userClubRole) {
                    DocumentRequestUser::create([
                        'document_request_id' => $documentRequest->id,
                        'user_id' => $userId
                    ]);
                }
            }

            return redirect()->route('backend.club.document-requests.show', $documentRequest)
                ->with('success', 'Yêu cầu nộp văn bản đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo yêu cầu nộp văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified document request
     */
    public function show(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document request belongs to user's club
        if ($documentRequest->club_id !== $clubId) {
            return redirect()->route('backend.club.document-requests.index')
                ->with('error', 'Bạn không có quyền xem yêu cầu này!');
        }

        // Load relationships with document submission status
        $documentRequest->load([
            'documentType',
            'requestedByUser',
            'documentRequestUsers.user',
            'documentRequestUsers.document'
        ]);

        return view('clubs.document-requests.show', compact('documentRequest'));
    }

    /**
     * Show the form for editing the specified document request
     */
    public function edit(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document request belongs to user's club
        if ($documentRequest->club_id !== $clubId) {
            return redirect()->route('backend.club.document-requests.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa yêu cầu này!');
        }

        // Can only edit if deadline hasn't passed and no documents submitted yet
        if ($documentRequest->deadline < now()) {
            return redirect()->route('backend.club.document-requests.show', $documentRequest)
                ->with('error', 'Không thể chỉnh sửa yêu cầu đã hết hạn!');
        }

        $hasSubmissions = DocumentRequestUser::where('document_request_id', $documentRequest->id)
            ->whereNotNull('document_id')
            ->exists();

        if ($hasSubmissions) {
            return redirect()->route('backend.club.document-requests.show', $documentRequest)
                ->with('error', 'Không thể chỉnh sửa yêu cầu vì đã có thành viên nộp văn bản!');
        }

        // Get active document types
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get club members
        $clubMembers = User::whereHas('userClubRoles', function ($query) use ($clubId) {
            $query->where('club_id', $clubId)
                ->where('status', 'active');
        })->orderBy('name')->get();

        // Get currently selected users
        $selectedUserIds = DocumentRequestUser::where('document_request_id', $documentRequest->id)
            ->pluck('user_id')
            ->toArray();

        return view('clubs.document-requests.edit', compact('documentRequest', 'documentTypes', 'clubMembers', 'selectedUserIds'));
    }

    /**
     * Update the specified document request
     */
    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document request belongs to user's club
        if ($documentRequest->club_id !== $clubId) {
            return redirect()->route('backend.club.document-requests.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa yêu cầu này!');
        }

        $validatedData = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'deadline' => 'required|date|after:today',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Verify document type belongs to club
        $documentType = DocumentType::where('id', $validatedData['document_type_id'])
            ->where('club_id', $clubId)
            ->first();

        if (!$documentType) {
            return redirect()->back()->withInput()
                ->with('error', 'Loại văn bản không hợp lệ!');
        }

        try {
            // Update document request
            $documentRequest->update([
                'document_type_id' => $validatedData['document_type_id'],
                'deadline' => $validatedData['deadline']
            ]);

            // Delete existing request users (only those who haven't submitted)
            DocumentRequestUser::where('document_request_id', $documentRequest->id)
                ->whereNull('document_id')
                ->delete();

            // Add new request users (avoiding duplicates)
            $existingUserIds = DocumentRequestUser::where('document_request_id', $documentRequest->id)
                ->pluck('user_id')
                ->toArray();

            foreach ($validatedData['user_ids'] as $userId) {
                if (!in_array($userId, $existingUserIds)) {
                    // Verify user belongs to club
                    $userClubRole = UserClubRole::where('user_id', $userId)
                        ->where('club_id', $clubId)
                        ->first();

                    if ($userClubRole) {
                        DocumentRequestUser::create([
                            'document_request_id' => $documentRequest->id,
                            'user_id' => $userId
                        ]);
                    }
                }
            }

            return redirect()->route('backend.club.document-requests.show', $documentRequest)
                ->with('success', 'Yêu cầu nộp văn bản đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật yêu cầu nộp văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified document request
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document request belongs to user's club
        if ($documentRequest->club_id !== $clubId) {
            return redirect()->route('backend.club.document-requests.index')
                ->with('error', 'Bạn không có quyền xóa yêu cầu này!');
        }

        // Cannot delete if there are submitted documents
        $hasSubmissions = DocumentRequestUser::where('document_request_id', $documentRequest->id)
            ->whereNotNull('document_id')
            ->exists();

        if ($hasSubmissions) {
            return redirect()->route('backend.club.document-requests.show', $documentRequest)
                ->with('error', 'Không thể xóa yêu cầu đã có người nộp văn bản!');
        }

        try {
            // Delete related request users
            DocumentRequestUser::where('document_request_id', $documentRequest->id)->delete();

            $documentRequest->delete();

            return redirect()->route('backend.club.document-requests.index')
                ->with('success', 'Yêu cầu nộp văn bản đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa yêu cầu nộp văn bản: ' . $e->getMessage());
        }
    }
}
