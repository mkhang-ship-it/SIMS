<?php

/**
 * UserDocumentController - Xử lý tài liệu từ phía User trong hệ thống multi-club
 * - Fixed: Sửa lỗi tìm kiếm từ 'name' thành 'title' để khớp với database schema
 * - Added: Debug method để kiểm tra quyền truy cập tài liệu
 * - Updated: Bỏ filter theo club_id - hiện xem tài liệu từ tất cả CLB theo quyền truy cập
 * - User xem các yêu cầu nộp tài liệu của mình
 * - Upload tài liệu theo yêu cầu
 * - Theo dõi trạng thái tài liệu (pending/approved/rejected)
 * - Xem các tài liệu có quyền truy cập (public, private của mình, restricted được chia sẻ) từ TẤT CẢ CLB
 * - Added: Xóa tài liệu do chính user tạo (không liên quan đến yêu cầu nộp)
 * - Updated: Thêm tính năng chỉ định người dùng cụ thể cho quyền "restricted"
 * - Added: Trang show để xem chi tiết tài liệu với access permissions
 * - Added: Trang edit để chỉnh sửa tên tài liệu và quyền truy cập
 */

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestUser;
use App\Models\DocumentType;
use App\Models\DocumentUserAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserDocumentController extends Controller
{
    /**
     * Display user's document requests (tasks to complete)
     */
    public function myRequests(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $query = DocumentRequestUser::with(['documentRequest.documentType', 'document'])
            ->whereHas('documentRequest', function ($q) use ($clubId) {
                $q->where('club_id', $clubId);
            })
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('document_id');
            } elseif ($request->status === 'completed') {
                $query->whereNotNull('document_id');
            }
        }

        // Filter by deadline status
        if ($request->filled('deadline_status')) {
            if ($request->deadline_status === 'expired') {
                $query->whereHas('documentRequest', function ($q) {
                    $q->where('deadline', '<', now());
                });
            } elseif ($request->deadline_status === 'active') {
                $query->whereHas('documentRequest', function ($q) {
                    $q->where('deadline', '>=', now());
                });
            }
        }

        $documentRequestUsers = $query->orderBy('created_at', 'desc')->get();

        return view('clubs.my-documents.requests', compact('documentRequestUsers'));
    }

    /**
     * Display user's uploaded documents
     */
    public function myDocuments(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $query = Document::with(['documentType'])
            ->where('club_id', $clubId)
            ->where('uploaded_by', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document type
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        // Get document types for filter
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('clubs.my-documents.index', compact('documents', 'documentTypes'));
    }

    /**
     * Display documents user has access to view (from all clubs)
     */
    public function accessibleDocuments(Request $request)
    {
        $user = Auth::user();

        $query = Document::with(['documentType', 'uploadedByUser', 'club'])
            ->where('status', 'approved')
            ->where(function ($q) use ($user) {
                // Public documents
                $q->where('visibility', 'public')
                    // Or user's own documents
                    ->orWhere('uploaded_by', $user->id)
                    // Or restricted documents user has access to
                    ->orWhere(function ($subQ) use ($user) {
                        $subQ->where('visibility', 'restricted')
                            ->whereHas('accessUsers', function ($accessQ) use ($user) {
                                $accessQ->where('user_id', $user->id);
                            });
                    });
            });

        // Filter by document type
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        // Search by document title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        // Get document types for filter (from all clubs user has access to)
        $documentTypes = DocumentType::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('clubs.my-documents.accessible', compact('documents', 'documentTypes'));
    }

    /**
     * Show form to upload document for a specific request
     */
    public function uploadForm(DocumentRequestUser $documentRequestUser)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if this request belongs to the user and club
        if (
            $documentRequestUser->user_id !== $user->id ||
            $documentRequestUser->documentRequest->club_id !== $clubId
        ) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Bạn không có quyền upload cho yêu cầu này!');
        }

        // Check if deadline has passed
        if ($documentRequestUser->documentRequest->deadline < now()) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Yêu cầu này đã hết hạn nộp!');
        }

        // Check if already uploaded
        if ($documentRequestUser->document_id) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Bạn đã nộp tài liệu cho yêu cầu này rồi!');
        }

        $documentRequestUser->load(['documentRequest.documentType']);

        return view('clubs.my-documents.upload', compact('documentRequestUser'));
    }

    /**
     * Store uploaded document
     */
    public function upload(Request $request, DocumentRequestUser $documentRequestUser)
    {
        $user = Auth::user();

        // Kiểm tra quyền nộp (user có được giao yêu cầu này không)
        if ($documentRequestUser->user_id !== $user->id) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Bạn không có quyền upload cho yêu cầu này!');
        }

        // Kiểm tra hạn nộp
        if ($documentRequestUser->documentRequest->deadline < now()) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Yêu cầu này đã hết hạn nộp!');
        }

        // Kiểm tra nếu đã nộp
        if ($documentRequestUser->document_id) {
            return redirect()->route('backend.club.my-documents.requests')
                ->with('error', 'Bạn đã nộp tài liệu cho yêu cầu này rồi!');
        }

        $validatedData = $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
            'title' => 'required|string|max:255',
        ]);

        try {
            // Upload file
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            // Lấy club_id từ yêu cầu gốc (thay vì session)
            $clubId = $documentRequestUser->documentRequest->club_id;

            // Tạo bản ghi document
            $document = Document::create([
                'club_id' => $clubId,
                'document_type_id' => $documentRequestUser->documentRequest->document_type_id,
                'uploaded_by' => $user->id,
                'title' => $validatedData['title'],
                'file_path' => $filePath,
                'visibility' => 'private',
                'status' => 'pending'
            ]);

            // Cập nhật liên kết yêu cầu
            $documentRequestUser->update([
                'document_id' => $document->id,
                'submitted_at' => now()
            ]);

            return redirect()->route('backend.club.my-documents.requests')
                ->with('success', 'Tài liệu đã được nộp thành công! Đang chờ duyệt.');
        } catch (\Exception $e) {
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi upload tài liệu: ' . $e->getMessage());
        }
    }


    /**
     * Show form to upload independent document (not from request)
     */
    public function createForm()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get active document types
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get club users for restricted visibility selection
        $clubUsers = \App\Models\Club::find($clubId)
            ->users()
            ->where('user_club_roles.status', 'active')
            ->where('users.id', '!=', $user->id) // Exclude current user
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return view('clubs.my-documents.create', compact('documentTypes', 'clubUsers'));
    }

    /**
     * Store independent document
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'visibility' => 'required|in:public,private,restricted',
            'allowed_users' => 'nullable|array',
            'allowed_users.*' => 'exists:users,id'
        ]);

        // Validate that restricted visibility requires at least one user
        if ($validatedData['visibility'] === 'restricted') {
            if (empty($validatedData['allowed_users']) || count($validatedData['allowed_users']) === 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Khi chọn quyền "Hạn chế", bạn phải chỉ định ít nhất một người dùng!');
            }
        }

        // Verify document type belongs to club
        $documentType = DocumentType::where('id', $validatedData['document_type_id'])
            ->where('club_id', $clubId)
            ->first();

        if (!$documentType) {
            return redirect()->back()->withInput()
                ->with('error', 'Loại văn bản không hợp lệ!');
        }

        try {
            // Handle file upload
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            // Create document
            $document = Document::create([
                'club_id' => $clubId,
                'document_type_id' => $validatedData['document_type_id'],
                'uploaded_by' => $user->id,
                'title' => $validatedData['title'],
                'file_path' => $filePath,
                'visibility' => $validatedData['visibility'],
                'status' => 'approved' // Auto-approved since independent
            ]);

            // If visibility is restricted, save allowed users
            if ($validatedData['visibility'] === 'restricted' && !empty($validatedData['allowed_users'])) {
                foreach ($validatedData['allowed_users'] as $userId) {
                    DocumentUserAccess::create([
                        'document_id' => $document->id,
                        'user_id' => $userId,
                    ]);
                }
            }

            return redirect()->route('backend.club.my-documents.index')
                ->with('success', 'Tài liệu đã được tạo thành công!');
        } catch (\Exception $e) {
            // Delete uploaded file if exists
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo tài liệu: ' . $e->getMessage());
        }
    }

    /**
     * Show document details
     */
    public function show(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn không có quyền xem tài liệu này!');
        }

        // Check permissions based on visibility and ownership
        if (!$this->canViewDocument($document, $user)) {
            return redirect()->route('backend.club.my-documents.accessible')
                ->with('error', 'Bạn không có quyền xem tài liệu này!');
        }

        // Load relationships for display
        $document->load(['documentType', 'uploadedByUser', 'accessUsers']);

        return view('clubs.my-documents.show', compact('document'));
    }

    /**
     * Show edit form for document
     */
    public function edit(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa văn bản này!');
        }

        // Check if user is the owner
        if ($document->uploaded_by !== $user->id) {
            return redirect()->route('backend.club.my-documents.show', $document)
                ->with('error', 'Bạn chỉ có thể chỉnh sửa văn bản do chính mình tạo!');
        }

        // Load necessary data
        $document->load(['documentType', 'accessUsers']);

        // Get club users for restricted visibility selection
        $clubUsers = \App\Models\Club::find($clubId)
            ->users()
            ->where('user_club_roles.status', 'active')
            ->where('users.id', '!=', $user->id) // Exclude current user
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return view('clubs.my-documents.edit', compact('document', 'clubUsers'));
    }

    /**
     * Update document information and access permissions
     */
    public function update(Request $request, Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa văn bản này!');
        }

        // Check if user is the owner
        if ($document->uploaded_by !== $user->id) {
            return redirect()->route('backend.club.my-documents.show', $document)
                ->with('error', 'Bạn chỉ có thể chỉnh sửa văn bản do chính mình tạo!');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'visibility' => 'required|in:public,private,restricted',
            'allowed_users' => 'nullable|array',
            'allowed_users.*' => 'exists:users,id'
        ]);

        // Validate that restricted visibility requires at least one user
        if ($validatedData['visibility'] === 'restricted') {
            if (empty($validatedData['allowed_users']) || count($validatedData['allowed_users']) === 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Khi chọn quyền "Hạn chế", bạn phải chỉ định ít nhất một thành viên!');
            }
        }

        try {
            // Update document basic info
            $document->update([
                'title' => $validatedData['title'],
                'visibility' => $validatedData['visibility'],
            ]);

            // Handle user access for restricted visibility
            if ($validatedData['visibility'] === 'restricted') {
                // Remove existing access
                DocumentUserAccess::where('document_id', $document->id)->delete();

                // Add new access
                foreach ($validatedData['allowed_users'] as $userId) {
                    DocumentUserAccess::create([
                        'document_id' => $document->id,
                        'user_id' => $userId,
                    ]);
                }
            } else {
                // Remove all access if not restricted
                DocumentUserAccess::where('document_id', $document->id)->delete();
            }

            return redirect()->route('backend.club.my-documents.show', $document)
                ->with('success', 'Văn bản đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Download document file
     */
    public function download(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn không có quyền tải xuống văn bản này!');
        }

        // Check permissions based on visibility and ownership
        if (!$this->canViewDocument($document, $user)) {
            return redirect()->route('backend.club.my-documents.accessible')
                ->with('error', 'Bạn không có quyền tải xuống văn bản này!');
        }

        // Check if file exists
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()
                ->with('error', 'File văn bản không tồn tại!');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $fileName = $document->original_filename ?? basename($document->file_path);

        return response()->download($filePath, $fileName);
    }

    /**
     * Delete user's own document (not related to document requests)
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn không có quyền xóa văn bản này!');
        }

        // Check if user is the owner
        if ($document->uploaded_by !== $user->id) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Bạn chỉ có thể xóa văn bản do chính mình tạo!');
        }

        // Check if document is related to a document request
        if ($document->requestUser()->exists()) {
            return redirect()->route('backend.club.my-documents.index')
                ->with('error', 'Không thể xóa văn bản liên quan đến yêu cầu nộp!');
        }

        try {
            // Delete file from storage
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete document from database
            $document->delete();

            return redirect()->route('backend.club.my-documents.index')
                ->with('success', 'Văn bản đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Check if user can view document
     */
    private function canViewDocument(Document $document, $user): bool
    {
        // Owner can always view their own document
        if ($document->uploaded_by === $user->id) {
            return true;
        }

        // Only approved documents are viewable by others
        if ($document->status !== 'approved') {
            return false;
        }

        // Check visibility
        switch ($document->visibility) {
            case 'public':
                return true;
            case 'private':
                return false; // Only owner can view
            case 'restricted':
                return DocumentUserAccess::where('document_id', $document->id)
                    ->where('user_id', $user->id)
                    ->exists();
            default:
                return false;
        }
    }
}
