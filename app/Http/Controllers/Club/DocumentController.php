<?php

/**
 * DocumentController - Quản lý tài liệu cho hệ thống multi-club
 * - Quản lý danh sách tài liệu đã upload
 * - Duyệt, từ chối tài liệu theo quyền
 * - Hỗ trợ phân tách dữ liệu theo từng CLB (club_id)
 * - Quản lý quyền xem tài liệu (public, private, restricted)
 */

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentUserAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents for current club
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $query = Document::with(['documentType', 'uploadedByUser', 'club'])
            ->where('club_id', $clubId);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document type if provided
        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->document_type_id);
        }

        // Search by document title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get document types for filter
        $documentTypes = DocumentType::where('club_id', $clubId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('clubs.documents.index', compact('documents', 'documentTypes'));
    }

    /**
     * Show the specified document
     */
    public function show(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền xem văn bản này!');
        }

        // Check permissions based on visibility
        if (!$this->canViewDocument($document, $user)) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền xem văn bản này!');
        }

        return view('clubs.documents.show', compact('document'));
    }

    /**
     * Update the specified document (approve/reject)
     */
    public function updateStatus(Request $request, Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa văn bản này!');
        }

        $validatedData = $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason_rejected' => 'nullable|string|max:1000'
        ]);

        try {
            $document->update([
                'status' => $validatedData['status'],
                'reason_rejected' => $validatedData['reason_rejected'] ?? null,
                'reviewed_by' => $user->id,
                'reviewed_at' => now()
            ]);

            $statusText = $validatedData['status'] === 'approved' ? 'duyệt' : 'từ chối';
            return redirect()->route('backend.club.documents.show', $document)
                ->with('success', "Văn bản đã được {$statusText} thành công!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Download the document file
     */
    public function download(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền tải xuống văn bản này!');
        }

        // Check permissions based on visibility
        if (!$this->canViewDocument($document, $user)) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền tải xuống văn bản này!');
        }

        // Check if file exists
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'File văn bản không tồn tại!');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $fileName = $document->original_filename ?? basename($document->file_path);

        return response()->download($filePath, $fileName);
    }

    /**
     * Update document visibility
     */
    public function updateVisibility(Request $request, Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa văn bản này!');
        }

        $validatedData = $request->validate([
            'visibility' => 'required|in:public,private,restricted'
        ]);

        try {
            $document->update([
                'visibility' => $validatedData['visibility']
            ]);

            return redirect()->route('backend.club.documents.show', $document)
                ->with('success', 'Quyền truy cập văn bản đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật quyền truy cập văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Manage restricted access for document
     */
    public function manageAccess(Request $request, Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền quản lý quyền truy cập văn bản này!');
        }

        if ($request->isMethod('post')) {
            $validatedData = $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            try {
                // Remove existing access
                DocumentUserAccess::where('document_id', $document->id)->delete();

                // Add new access
                foreach ($validatedData['user_ids'] as $userId) {
                    DocumentUserAccess::create([
                        'document_id' => $document->id,
                        'user_id' => $userId
                    ]);
                }

                return redirect()->route('backend.club.documents.show', $document)
                    ->with('success', 'Quyền truy cập văn bản đã được cập nhật thành công!');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra khi cập nhật quyền truy cập văn bản: ' . $e->getMessage());
            }
        }

        // Get current access users
        $accessUsers = $document->accessUsers;

        // Get all club members for selection
        $clubMembers = User::whereHas('userClubRoles', function ($query) use ($clubId) {
            $query->where('club_id', $clubId);
        })->orderBy('name')->get();

        return view('clubs.documents.manage-access', compact('document', 'accessUsers', 'clubMembers'));
    }

    /**
     * Remove the specified document
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document belongs to user's club
        if ($document->club_id !== $clubId) {
            return redirect()->route('backend.club.documents.index')
                ->with('error', 'Bạn không có quyền xóa văn bản này!');
        }

        try {
            // Delete associated file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete related access records
            DocumentUserAccess::where('document_id', $document->id)->delete();

            $document->delete();

            return redirect()->route('backend.club.documents.index')
                ->with('success', 'Văn bản đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Check if user can view document based on visibility settings
     */
    private function canViewDocument(Document $document, User $user): bool
    {
        // Admin always can view
        if ($user->hasPermissionForModuleCode('document_management', 'view_all', 'branch')) {
            return true;
        }

        // Owner can always view their own document
        if ($document->uploaded_by === $user->id) {
            return true;
        }

        // Check visibility
        switch ($document->visibility) {
            case 'public':
                return true;
            case 'private':
                return false; // Only owner and admin can view
            case 'restricted':
                return DocumentUserAccess::where('document_id', $document->id)
                    ->where('user_id', $user->id)
                    ->exists();
            default:
                return false;
        }
    }
}
