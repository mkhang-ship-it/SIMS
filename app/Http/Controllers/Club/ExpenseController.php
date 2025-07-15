<?php
// Updated: Refactored ExpenseController to match new DB structure and code style of MemberController
// - Use session('club_id') for club context
// - Eager load relationships (requestedBy, approvedBy, semester, transactions)
// - Use UUID for IDs, update all CRUD and approval logic
// - Consistent permission checks and error handling
// - Updated comments and structure for clarity
// - No view changes required unless DB structure changes

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Semester;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Lấy tham số lọc
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Lấy danh sách học kỳ cho bộ lọc
        $semesters = Semester::orderBy('created_at', 'desc')->where('status', 'active')->get();

        // Xây dựng query
        $query = Expense::with(['requestedBy', 'approvedBy', 'semester', 'transactions'])
            ->where('club_id', $clubId);

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

    public function store(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            DB::beginTransaction();
            Expense::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'requested_at' => now(),
                'status' => 'approved',
                'club_id' => $clubId,
                'semester_id' => $request->semester_id,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Chi phí đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        if ($expense->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền chỉnh sửa yêu cầu chi phí này.');
        }
        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể chỉnh sửa chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'semester_id' => $request->semester_id,
            ]);
            return redirect()->back()->with('success', 'Chi phí đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật chi phí: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        if ($expense->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền xóa yêu cầu chi phí này.');
        }
        if ($expense->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa chi phí đang chờ phê duyệt.');
        }

        try {
            $expense->delete();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được xóa thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    public function approve(Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        if ($expense->club_id !== $clubId) {
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

    public function reject(Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        if ($expense->club_id !== $clubId) {
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

    public function markAsPaid(Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        if ($expense->club_id !== $clubId) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật trạng thái thanh toán.');
        }
        if ($expense->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể đánh dấu đã thanh toán cho yêu cầu chi phí đã được phê duyệt.');
        }

        try {
            $expense->update([
                'status' => 'paid',
            ]);
            Transaction::create([
                'expense_id' => $expense->id,
                'paid_amount' => $expense->amount,
                'type' => 'outcome',
                'description' => 'Thanh toán chi phí',
                'club_id' => $clubId,
                'semester_id' => $expense->semester_id,
                'payment_date' => now(),
                'payment_method' => 'Cash',
                'note' => 'Thanh toán chi phí ' . $expense->name,
            ]);
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được đánh dấu là đã thanh toán.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:expenses,id',
        ]);

        $expenses = Expense::whereIn('id', $request->expense_ids)
            ->where('club_id', $clubId)
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

    public function myExpenses(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Lấy tham số lọc
        $selectedSemester = $request->get('semester_id');
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Lấy danh sách học kỳ cho bộ lọc
        $semesters = Semester::orderBy('created_at', 'desc')->where('status', 'active')->get();

        // Xây dựng query - chỉ lấy chi phí của user hiện tại
        $query = Expense::with(['requestedBy', 'approvedBy', 'semester', 'transactions'])
            ->where('club_id', $clubId)
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

    public function myStore(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            DB::beginTransaction();
            Expense::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'description' => $request->description,
                'requested_by' => $user->id,
                'requested_at' => now(),
                'status' => 'pending',
                'club_id' => $clubId,
                'semester_id' => $request->semester_id,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Yêu cầu chi phí đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu chi phí: ' . $e->getMessage());
        }
    }

    public function myUpdate(Request $request, Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        if ($expense->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền chỉnh sửa yêu cầu chi phí này.');
        }
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

    public function myDestroy(Expense $expense)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        if ($expense->club_id !== $clubId) {
            abort(403, 'Bạn không có quyền xóa yêu cầu chi phí này.');
        }
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

    public function myBulkDestroy(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:expenses,id',
        ]);

        $expenses = Expense::whereIn('id', $request->expense_ids)
            ->where('club_id', $clubId)
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
