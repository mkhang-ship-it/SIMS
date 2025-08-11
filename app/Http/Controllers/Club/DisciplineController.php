<?php

/**
 * Updated: DisciplineController - Added Excel import functionality for disciplines
 * - Import disciplines from Excel with fields: name (Tên), discipline_points (Điểm), description (Mô tả)
 * - Excel headers should be: "Tên", "Điểm", "Mô tả"
 * - Validates imported data and provides feedback on success/errors
 */

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discipline;
use App\Imports\DisciplineImport;
use App\Exports\DisciplineTemplateExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DisciplineController extends Controller
{
    /**
     * Display a listing of disciplines for current club
     */
    public function index()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $disciplines = Discipline::where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.disciplines.index', compact('disciplines'));
    }

    /**
     * Store a newly created discipline
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:disciplines,code,NULL,id,club_id,' . $clubId,
            'discipline_points' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $disciplineData = [
                'name' => $validatedData['name'],
                'club_id' => $clubId,
                'discipline_points' => $validatedData['discipline_points'],
                'description' => $validatedData['description'],
                'status' => $validatedData['status']
            ];

            // Only set code if provided
            if (!empty($validatedData['code'])) {
                $disciplineData['code'] = $validatedData['code'];
            }

            Discipline::create($disciplineData);

            return redirect()->route('backend.club.disciplines.index')
                ->with('success', 'Loại kỷ luật đã được tạo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo loại kỷ luật: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified discipline
     */
    public function update(Request $request, Discipline $discipline)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if discipline belongs to user's club
        if ($discipline->club_id !== $clubId) {
            return redirect()->route('backend.club.disciplines.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa loại kỷ luật này!');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:disciplines,code,' . $discipline->id . ',id,club_id,' . $clubId,
            'discipline_points' => 'required|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $updateData = [
                'name' => $validatedData['name'],
                'discipline_points' => $validatedData['discipline_points'],
                'description' => $validatedData['description'],
                'status' => $validatedData['status']
            ];

            // Only update code if provided and different from current
            if (!empty($validatedData['code']) && $validatedData['code'] !== $discipline->code) {
                $updateData['code'] = $validatedData['code'];
            }

            $discipline->update($updateData);

            return redirect()->route('backend.club.disciplines.index')
                ->with('success', 'Loại kỷ luật đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật loại kỷ luật: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified discipline
     */
    public function destroy(Discipline $discipline)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Check if discipline belongs to user's club
        if ($discipline->club_id !== $clubId) {
            return redirect()->route('backend.club.disciplines.index')
                ->with('error', 'Bạn không có quyền xóa loại kỷ luật này!');
        }

        try {
            $discipline->delete();

            return redirect()->route('backend.club.disciplines.index')
                ->with('success', 'Loại kỷ luật đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xóa loại kỷ luật: ' . $e->getMessage());
        }
    }

    /**
     * Import disciplines from Excel file
     */
    public function import(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $validatedData = $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv'
        ], [
            'import_file.required' => 'Vui lòng chọn file để import.',
            'import_file.file' => 'File upload không hợp lệ.',
            'import_file.mimes' => 'File phải có định dạng Excel (.xlsx, .xls) hoặc CSV.'
        ]);

        try {
            $import = new DisciplineImport($clubId);
            Excel::import($import, $request->file('import_file'));

            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();

            $message = "Import thành công {$importedCount} loại kỷ luật!";

            if (!empty($errors)) {
                $errorMessage = implode("\n", $errors);
                $message .= " Một số lỗi xảy ra:\n" . $errorMessage;

                return redirect()->route('backend.club.disciplines.index')
                    ->with('warning', $message);
            }

            return redirect()->route('backend.club.disciplines.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Dòng {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->with('error', 'Lỗi validation khi import:\n' . implode('\n', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }

    /**
     * Export discipline import template
     */
    public function exportTemplate()
    {
        $user = Auth::user();
        $clubId = session('club_id');

        try {
            return Excel::download(
                new DisciplineTemplateExport(),
                'File Mẫu Import Kỷ Luật.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải file mẫu: ' . $e->getMessage());
        }
    }
}
