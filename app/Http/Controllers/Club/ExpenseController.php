<?php
// ExpenseController - Đã sửa lại cấu trúc giống InvoiceController:
// - Gom nhóm các phương thức, thêm comment cho từng method
// - Chuẩn hóa lấy filter, query, statistics, trả về view
// - Thêm transaction cho các thao tác ghi/xóa
// - Đặt lại tên biến, thứ tự, và xử lý lỗi nhất quán
// - Thêm các phương thức cho trang Yêu Cầu Thanh Toán của user (myExpenses, myStore, myUpdate, myDestroy, myBulkDestroy)

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Semester;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    /**
     * Hiển thị danh sách các yêu cầu chi phí của CLB hiện tại
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền xem danh sách yêu cầu chi phí.');
        }

        // Lấy tham số lọc
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Lấy danh sách học kỳ cho bộ lọc
        $semesters = Semester::orderBy('created_at', 'desc')->where('status', 'active')->get();

        // Xây dựng query
        $query = Expense::with(['requestedBy', 'approvedBy', 'semester', 'transactions'])
            ->where('club_id', $club->id);

        // Áp dụng bộ lọc
        if ($selectedSemester) {
            $query->where('semester_id', $selectedSemester);
        }
        if ($selectedMonth && $selectedYear) {
            $query->whereYear('requested_at', $selectedYear)
                ->whereMonth('requested_at', $selectedMonth);
        }

        $expenses = $query->orderBy('requested_at', 'desc')->get();

        // Thống kê
        $statistics = [
            'total' => $expenses->count(),
            'pending' => $expenses->where('status', 'pending')->count(),
            'approved' => $expenses->where('status', 'approved')->count(),
            'rejected' => $expenses->where('status', 'rejected')->count(),
            'paid' => $expenses->where('status', 'paid')->count(),
            'total_amount' => $expenses->sum('amount'),
            'pending_amount' => $expenses->where('status', 'pending')->sum('amount'),
            'approved_amount' => $expenses->where('status', 'approved')->sum('amount'),
            'rejected_amount' => $expenses->where('status', 'rejected')->sum('amount'),
            'paid_amount' => $expenses->where('status', 'paid')->sum('amount'),
        ];

        return view('clubs.expenses.index', compact(
            'expenses',
            'statistics',
            'semesters',
            'selectedSemester',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Tạo mới một yêu cầu chi phí
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền tạo yêu cầu chi phí.');
        }

        try {
            DB::beginTransaction();

            Expense::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'requested_by' => $user->id,
                'requested_at' => now(),
                'status' => 'pending',
                'club_id' => $club->id,
                'semester_id' => $request->semester_id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật một yêu cầu chi phí (chỉ khi đang chờ duyệt)
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa yêu cầu chi phí này.');
        }
        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa yêu cầu chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'semester_id' => $request->semester_id,
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Xóa một yêu cầu chi phí (chỉ khi đang chờ duyệt)
     */
    public function destroy(Expense $expense)
    {
        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền xóa yêu cầu chi phí này.');
        }
        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa yêu cầu chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->delete();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Phê duyệt một yêu cầu chi phí (chỉ khi đang chờ duyệt)
     */
    public function approve(Expense $expense)
    {
        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền phê duyệt yêu cầu chi phí này.');
        }
        if (!$expense->canBeApproved()) {
            return redirect()->back()->with('error', 'Bạn không có quyền phê duyệt hoặc yêu cầu chi phí không ở trạng thái chờ phê duyệt.');
        }

        try {
            $expense->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được phê duyệt thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi phê duyệt yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối một yêu cầu chi phí (chỉ khi đang chờ duyệt)
     */
    public function reject(Expense $expense)
    {
        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền từ chối yêu cầu chi phí này.');
        }

        if (!$expense->canBeRejected()) {
            return redirect()->back()->with('error', 'Bạn không có quyền từ chối hoặc yêu cầu chi phí không ở trạng thái chờ phê duyệt.');
        }

        try {
            $expense->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được từ chối.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi từ chối yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu đã thanh toán cho yêu cầu chi phí (chỉ khi đã được phê duyệt)
     */
    public function markAsPaid(Expense $expense)
    {
        $user = Auth::user();
        $club = $user->club;

        if (!$club || $expense->club_id !== $club->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật trạng thái thanh toán.');
        }
        if ($expense->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể đánh dấu đã thanh toán cho yêu cầu chi phí đã được phê duyệt.');
        }

        try {
            $expense->update([
                'status' => 'paid',
            ]);

            // Tạo transaction
            Transaction::create([
                'expense_id' => $expense->id,
                'paid_amount' => $expense->amount,
                'type' => 'outcome',
                'description' => 'Thanh toán chi phí',
                'club_id' => $club->id,
                'semester_id' => $expense->semester_id,
                'payment_date' => now(),
                'payment_method' => 'cash',
                'note' => 'Thanh toán chi phí ' . $expense->name,
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được đánh dấu là đã thanh toán.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán: ' . $e->getMessage());
        }
    }

    /**
     * Xóa nhiều yêu cầu chi phí cùng lúc (chỉ khi đang chờ duyệt)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:expenses,id'
        ]);

        $user = Auth::user();
        $club = $user->club;

        if ($club->id !== $user->club_id) {
            abort(403, 'Bạn không có quyền xóa yêu cầu chi phí này.');
        }

        $expenses = Expense::whereIn('id', $request->expense_ids)
            ->where('club_id', $club->id)
            ->where('status', 'pending')
            ->get();

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'Không có yêu cầu chi phí nào được chọn để xóa.');
        }

        try {
            DB::beginTransaction();
            foreach ($expenses as $expense) {
                $expense->delete();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Đã xóa ' . $expenses->count() . ' yêu cầu chi phí thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách yêu cầu chi phí của user hiện tại
     */
    public function myExpenses(Request $request)
    {
        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            abort(403, 'Bạn không thuộc về CLB nào.');
        }

        // Lấy tham số lọc
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Lấy danh sách học kỳ cho bộ lọc
        $semesters = Semester::orderBy('created_at', 'desc')->where('status', 'active')->get();

        // Xây dựng query - chỉ lấy chi phí của user hiện tại
        $query = Expense::with(['requestedBy', 'approvedBy', 'semester', 'transactions'])
            ->where('club_id', $club->id)
            ->where('requested_by', $user->id);

        // Áp dụng bộ lọc
        if ($selectedSemester) {
            $query->where('semester_id', $selectedSemester);
        }
        if ($selectedMonth && $selectedYear) {
            $query->whereYear('requested_at', $selectedYear)
                ->whereMonth('requested_at', $selectedMonth);
        }

        $expenses = $query->orderBy('requested_at', 'desc')->get();

        // Thống kê
        $statistics = [
            'total' => $expenses->count(),
            'pending' => $expenses->where('status', 'pending')->count(),
            'approved' => $expenses->where('status', 'approved')->count(),
            'rejected' => $expenses->where('status', 'rejected')->count(),
            'paid' => $expenses->where('status', 'paid')->count(),
            'total_amount' => $expenses->sum('amount'),
            'pending_amount' => $expenses->where('status', 'pending')->sum('amount'),
            'approved_amount' => $expenses->where('status', 'approved')->sum('amount'),
            'rejected_amount' => $expenses->where('status', 'rejected')->sum('amount'),
            'paid_amount' => $expenses->where('status', 'paid')->sum('amount'),
        ];

        return view('clubs.expenses.my-expenses', compact(
            'expenses',
            'statistics',
            'semesters',
            'selectedSemester',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Tạo mới một yêu cầu chi phí (cho user)
     */
    public function myStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            abort(403, 'Bạn không thuộc về CLB nào.');
        }

        try {
            DB::beginTransaction();

            Expense::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'requested_by' => $user->id,
                'requested_at' => now(),
                'status' => 'pending',
                'club_id' => $club->id,
                'semester_id' => $request->semester_id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật một yêu cầu chi phí của user (chỉ khi đang chờ duyệt)
     */
    public function myUpdate(Request $request, Expense $expense)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            abort(403, 'Bạn không thuộc về CLB nào.');
        }

        // Kiểm tra quyền sở hữu
        if ($expense->requested_by !== $user->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa yêu cầu chi phí này.');
        }

        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa yêu cầu chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'semester_id' => $request->semester_id,
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Xóa một yêu cầu chi phí của user (chỉ khi đang chờ duyệt)
     */
    public function myDestroy(Expense $expense)
    {
        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            abort(403, 'Bạn không thuộc về CLB nào.');
        }

        // Kiểm tra quyền sở hữu
        if ($expense->requested_by !== $user->id) {
            abort(403, 'Bạn không có quyền xóa yêu cầu chi phí này.');
        }

        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa yêu cầu chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->delete();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    /**
     * Xóa nhiều yêu cầu chi phí của user cùng lúc (chỉ khi đang chờ duyệt)
     */
    public function myBulkDestroy(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:expenses,id'
        ]);

        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            abort(403, 'Bạn không thuộc về CLB nào.');
        }

        $expenses = Expense::whereIn('id', $request->expense_ids)
            ->where('club_id', $club->id)
            ->where('requested_by', $user->id)
            ->where('status', 'pending')
            ->get();

        if ($expenses->isEmpty()) {
            return redirect()->back()->with('error', 'Không có yêu cầu chi phí nào được chọn để xóa.');
        }

        try {
            DB::beginTransaction();
            foreach ($expenses as $expense) {
                $expense->delete();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Đã xóa ' . $expenses->count() . ' yêu cầu chi phí thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa yêu cầu chi phí: ' . $e->getMessage());
        }
    }
}
