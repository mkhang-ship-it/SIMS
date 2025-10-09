<?php

/**
 * Updated: DocumentTypeController - Added Excel import functionality for document types
 * - Import document types from Excel with fields: name (Tên), code (Mã), description (Mô tả)
 * - Excel headers should be: "Tên", "Mã", "Mô tả"
 * - Validates imported data and provides feedback on success/errors
 */

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Club;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of document types for current club
     */
    public function index()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $documentTypes = DocumentType::where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.document-types.index', compact('documentTypes'));
    }

    /**
     * Store a newly created document type
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:document_types,code,NULL,id,club_id,' . $clubId,
            'description' => 'nullable|string',
            'template_file' => 'nullable|file|mimes:docx|max:10240', // thêm nullable để không bắt buộc upload
            'status' => 'required|in:active,inactive'
        ]);

        try {
            // Tạo dữ liệu cơ bản
            $documentTypeData = [
                'name' => $validatedData['name'],
                'club_id' => $clubId,
                'description' => $validatedData['description'] ?? null,
                'status' => $validatedData['status']
            ];

            // Xử lý file upload (chỉ nếu có)
            if ($request->hasFile('template_file')) {
                $file = $request->file('template_file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('document-templates', $fileName, 'public');
                $documentTypeData['template_file_path'] = $filePath;
            }

            // Thêm code nếu có nhập
            if (!empty($validatedData['code'])) {
                $documentTypeData['code'] = $validatedData['code'];
            }

            DocumentType::create($documentTypeData);

            return redirect()->route('backend.club.document-types.index')
                ->with('success', 'Danh mục văn bản đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo danh mục văn bản: ' . $e->getMessage());
        }
    }


    /**
     * Update the specified document type
     */
    /**
     * Update the specified document type
     * - Xóa file cũ nếu upload file mới
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document type belongs to user's club
        if ($documentType->club_id !== $clubId) {
            return redirect()->route('backend.club.document-types.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa danh mục văn bản này!');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:document_types,code,' . $documentType->id . ',id,club_id,' . $clubId,
            'description' => 'nullable|string',
            'template_file' => 'nullable|file|mimes:docx|max:10240', // 10MB max, chỉ cho phép DOCX
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $updateData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'status' => $validatedData['status']
            ];

            // Handle file upload if new file is provided
            if ($request->hasFile('template_file')) {
                // Xóa file cũ nếu có
                if ($documentType->template_file_path && Storage::disk('public')->exists($documentType->template_file_path)) {
                    Storage::disk('public')->delete($documentType->template_file_path);
                }
                $file = $request->file('template_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('document-templates', $fileName, 'public');
                $updateData['template_file_path'] = $filePath;
            }

            // Only update code if provided and different from current
            if (!empty($validatedData['code']) && $validatedData['code'] !== $documentType->code) {
                $updateData['code'] = $validatedData['code'];
            }

            $documentType->update($updateData);

            return redirect()->route('backend.club.document-types.index')
                ->with('success', 'Danh mục văn bản đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật danh mục văn bản: ' . $e->getMessage());
        }
    }

    /**
     * Download template file
     */
    public function downloadTemplate(DocumentType $documentType)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Kiểm tra quyền truy cập
        if ($documentType->club_id !== $clubId) {
            return redirect()->route('backend.club.document-types.index')
                ->with('error', 'Bạn không có quyền tải xuống file này!');
        }

        // Kiểm tra file tồn tại
        if (!$documentType->template_file_path || !Storage::disk('public')->exists($documentType->template_file_path)) {
            return redirect()->route('backend.club.document-types.index')
                ->with('error', 'File template không tồn tại!');
        }

        // Lấy đường dẫn tuyệt đối
        $templatePath = Storage::disk('public')->path($documentType->template_file_path);

        // Đọc template bằng PhpWord
        $template = new TemplateProcessor($templatePath);

        // Dữ liệu thay thế
        $club = Club::find($clubId);
        $data = [
            'full_name' => $user->name ?? '',
            'email_address' => $user->email ?? '',
            'phone_number' => $user->phone ?? '',
            'full_address' => $user->full_address ?? '',
            'identity_card_code' => $user->identity_card_code ?? '',
            'student_code' => $user->student_code ?? '',
            'birthday' => $user->birthday ?? '',
            'gender' => $user->gender === 'male'
                ? 'Nam'
                : ($user->gender === 'female' ? 'Nữ' : ''),
            'major' => $user->major->name ?? '',
            'course' => $user->course->name ?? '',

            'club_name' => $club->name ?? '',
            'club_phone' => $club->phone ?? '',
            'club_email' => $club->email ?? '',
            'club_address' => $club->full_address ?? ''
        ];

        // Gán các biến vào template
        foreach ($data as $key => $value) {
            $template->setValue("{{$key}}", $value);
        }

        // Lưu file tạm thời
        $outputFileName = time() . '_' . $documentType->name . '.docx';
        $tempPath = storage_path('app/temp/' . $outputFileName);
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }
        $template->saveAs($tempPath);

        // Trả file về client
        return response()->download($tempPath, $outputFileName)->deleteFileAfterSend(true);
    }

    /**
     * Remove the specified document type
     */
    public function destroy(DocumentType $documentType)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if document type belongs to user's club
        if ($documentType->club_id !== $clubId) {
            return redirect()->route('backend.club.document-types.index')
                ->with('error', 'Bạn không có quyền xóa danh mục văn bản này!');
        }

        try {
            // Delete associated file if exists
            if ($documentType->template_file_path && Storage::disk('public')->exists($documentType->template_file_path)) {
                Storage::disk('public')->delete($documentType->template_file_path);
            }

            $documentType->delete();

            return redirect()->route('backend.club.document-types.index')
                ->with('success', 'Danh mục văn bản đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa danh mục văn bản: ' . $e->getMessage());
        }
    }
}
