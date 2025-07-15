<?php
// InvoiceController - Refactored for new member structure using UserClubRole (multi-club membership)
// Features: CRUD operations, multi-member invoice creation, status management
// Modal-based interface: create/edit/delete invoices with member selection (UserClubRole)
// Payment tracking: record transactions, auto-update status, overdue management
// Permissions: only pending invoices can be edited/deleted
// Updated: All member queries now use UserClubRole for club membership validation and selection
// Updated: clubMembers now contains UserClubRole objects (with eager loaded user)
// Updated: All member-related validation and selection logic uses UserClubRole
// Updated: View data and member selection in forms now use UserClubRole
// Updated: All references to user->club_id replaced with session('club_id') or UserClubRole
// Updated: All member selection in forms/views should use UserClubRole (with user relation)

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Semester;
use App\Models\Club;
use App\Models\UserClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices for the current club
     */
    public function index(Request $request)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Get filter parameters
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Build query
        $query = Invoice::with(['user', 'semester', 'transactions'])
            ->where('club_id', $clubId);

        // Apply filters
        if ($selectedSemester) {
            $query->where('semester_id', $selectedSemester);
        }

        if ($selectedMonth && $selectedYear) {
            $query->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        // Calculate monthly statistics for current year
        $monthlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthInvoices = Invoice::with(['user', 'semester', 'transactions'])
                ->where('club_id', $clubId)
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->get();

            $monthlyStats[$month] = [
                'total' => $monthInvoices->count(),
                'pending' => $monthInvoices->where('status', 'pending')->count(),
                'paid' => $monthInvoices->where('status', 'paid')->count(),
                'overdue' => $monthInvoices->where('status', 'overdue')->count(),
                'total_amount' => $monthInvoices->sum('total_amount'),
                'paid_amount' => $monthInvoices->sum('total_paid'),
            ];
        }

        // Get statistics
        $statistics = [
            'total' => $invoices->count(),
            'pending' => $invoices->where('status', 'pending')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'paid_amount' => $invoices->sum('total_paid'),
            'monthly_stats' => $monthlyStats,
        ];

        // Get data for create form: club members via UserClubRole
        $clubMembers = UserClubRole::with(['user'])
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->get();

        $semesters = Semester::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.invoices.index', compact(
            'invoices',
            'statistics',
            'clubMembers',
            'semesters',
            'selectedSemester',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Store a newly created invoice for multiple members
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'semester_id' => 'required|exists:semesters,id',
            'member_ids' => 'required|array|min:1', // now expects user_club_role ids
            'member_ids.*' => 'exists:user_club_roles,id',
            'due_date' => 'nullable|date|after:today',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $clubId = session('club_id');

            // Validate that all selected user_club_roles belong to this club
            $selectedMembers = UserClubRole::with('user')
                ->whereIn('id', $request->member_ids)
                ->where('club_id', $clubId)
                ->where('status', 'active')
                ->get();

            if ($selectedMembers->count() !== count($request->member_ids)) {
                return redirect()->back()->withInput()->with('error', 'Một số thành viên không thuộc câu lạc bộ của bạn!');
            }

            $dueDate = $request->due_date ? Carbon::parse($request->due_date) : Carbon::now()->addDays(7);

            // Create invoices for each selected member (user)
            foreach ($selectedMembers as $member) {
                Invoice::create([
                    'user_id' => $member->user_id,
                    'name' => $request->name,
                    'total_amount' => $request->total_amount,
                    'due_date' => $dueDate,
                    'semester_id' => $request->semester_id,
                    'club_id' => $clubId,
                    'note' => $request->note,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            return redirect()->route('backend.club.invoices.index')
                ->with('success', "Đã tạo thành công {$selectedMembers->count()} hóa đơn cho các thành viên được chọn!");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Check if invoice belongs to user's club
        if ($invoice->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền xem hóa đơn này.');
        }

        $invoice->load(['user', 'semester', 'club', 'transactions']);

        return view('clubs.invoices.show', compact('invoice'));
    }

    /**
     * Update the specified invoice (only if pending)
     */
    public function update(Request $request, Invoice $invoice)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Check if invoice belongs to user's club
        if ($invoice->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền chỉnh sửa hóa đơn này.');
        }

        // Check if invoice can be edited
        if (!$invoice->canBeEdited()) {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa hóa đơn đang ở trạng thái chờ thanh toán!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $invoice->update([
                'name' => $request->name,
                'total_amount' => $request->total_amount,
                'due_date' => Carbon::parse($request->due_date),
                'note' => $request->note,
            ]);

            return redirect()->route('backend.club.invoices.index')
                ->with('success', 'Hóa đơn đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice (only if pending)
     */
    public function destroy(Invoice $invoice)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Check if invoice belongs to user's club
        if ($invoice->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền xóa hóa đơn này.');
        }

        // Check if invoice can be deleted
        if (!$invoice->canBeDeleted()) {
            return redirect()->back()->with('error', 'Chỉ có thể xóa hóa đơn đang ở trạng thái chờ thanh toán!');
        }

        try {
            $invoice->delete();

            return redirect()->route('backend.club.invoices.index')
                ->with('success', 'Hóa đơn đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Remove multiple invoices (only if pending)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        try {
            $clubId = session('club_id');
            $user = Auth::user();

            $invoices = Invoice::whereIn('id', $request->invoice_ids)
                ->where('club_id', $clubId)
                ->get();

            // Check if all invoices can be deleted
            $unDeletableInvoices = $invoices->filter(function ($invoice) {
                return !$invoice->canBeDeleted();
            });

            if ($unDeletableInvoices->count() > 0) {
                return redirect()->back()->with('error', 'Một số hóa đơn không thể xóa vì không ở trạng thái chờ thanh toán!');
            }

            DB::beginTransaction();

            $deletedCount = 0;
            foreach ($invoices as $invoice) {
                $invoice->delete();
                $deletedCount++;
            }

            DB::commit();

            return redirect()->route('backend.club.invoices.index')
                ->with('success', "Đã xóa thành công {$deletedCount} hóa đơn!");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa hóa đơn: ' . $e->getMessage());
        }
    }

    /**
     * Record a new payment transaction for an invoice
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Check if invoice belongs to user's club
        if ($invoice->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền ghi nhận thanh toán cho hóa đơn này.');
        }

        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_date' => 'required|date|after_or_equal:' . $invoice->created_at->format('Y-m-d'),
            'payment_method' => 'required|in:Sepay,PayOS,Cash',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            Transaction::create([
                'invoice_id' => $invoice->id,
                'type' => 'income',
                'paid_amount' => $request->paid_amount,
                'payment_date' => Carbon::parse($request->payment_date),
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            // Refresh invoice to get updated relationships
            $invoice->refresh();

            DB::commit();

            return redirect()->route('backend.club.invoices.show', $invoice)
                ->with('success', 'Giao dịch thanh toán đã được ghi nhận thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi ghi nhận thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Update overdue status for invoices that are past due date
     * This can be called manually or via scheduled command
     */
    public function updateOverdueStatus()
    {
        try {
            $updatedCount = Invoice::overdue()->update(['status' => 'overdue']);

            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái quá hạn cho {$updatedCount} hóa đơn.",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display invoices for the current member (My Invoices)
     */
    public function myInvoices(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get filter parameters
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Build query for current user's invoices in this club
        $query = Invoice::with(['semester', 'club', 'transactions'])
            ->where('user_id', $user->id)
            ->where('club_id', $clubId);

        // Apply filters
        if ($selectedSemester) {
            $query->where('semester_id', $selectedSemester);
        }

        if ($selectedMonth && $selectedYear) {
            $query->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        // Calculate monthly statistics for current year (for current user only)
        $monthlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthInvoices = Invoice::with(['semester', 'club', 'transactions'])
                ->where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->get();

            $monthlyStats[$month] = [
                'total' => $monthInvoices->count(),
                'pending' => $monthInvoices->where('status', 'pending')->count(),
                'paid' => $monthInvoices->where('status', 'paid')->count(),
                'overdue' => $monthInvoices->where('status', 'overdue')->count(),
                'total_amount' => $monthInvoices->sum('total_amount'),
                'paid_amount' => $monthInvoices->sum('total_paid'),
            ];
        }

        // Calculate statistics for current user's invoices
        $statistics = [
            'total' => $invoices->count(),
            'pending' => $invoices->where('status', 'pending')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'paid_amount' => $invoices->sum('total_paid'),
            'monthly_stats' => $monthlyStats,
        ];

        // Get semesters for filter dropdown
        $semesters = Semester::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.invoices.my_invoices', compact(
            'invoices',
            'statistics',
            'semesters',
            'selectedSemester',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Export the specified invoice as PDF (theo mẫu pdf_receipt)
     */
    public function exportPdf(Invoice $invoice)
    {
        $clubId = session('club_id');
        $user = Auth::user();
        // Check if invoice belongs to user's club
        if ($invoice->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền xuất hóa đơn này.');
        }

        $club = Club::find($invoice->club_id);

        $invoice->load(['user', 'semester', 'club', 'transactions']);

        // Configure PDF with Vietnamese support
        $pdf = Pdf::loadView('clubs.invoices.pdf_receipt', compact('invoice', 'club'));

        // Set PDF options for Vietnamese support
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVuSans',
            'chroot' => public_path(),
            'enable_font_subsetting' => true,
            'default_paper_size' => 'a4',
            'dpi' => 150,
            'font_height_ratio' => 0.9,
            'enable_php' => false,
            'enable_javascript' => false,
        ]);

        $filename = 'HoaDon_' . ($invoice->invoice_code ?? 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT)) . '.pdf';
        return $pdf->download($filename);
    }
}
