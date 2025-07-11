# Trang Yêu Cầu Thanh Toán (My Expenses)

## Tổng quan
Trang Yêu Cầu Thanh Toán là trang dành cho user (thành viên CLB) để quản lý các yêu cầu chi phí của mình. Trang này cho phép user tạo, xem, chỉnh sửa và xóa các yêu cầu chi phí của họ.

## Tính năng chính

### 1. Xem danh sách yêu cầu chi phí
- Hiển thị tất cả yêu cầu chi phí của user hiện tại
- Thống kê theo trạng thái: Tổng, Chờ phê duyệt, Đã phê duyệt, Bị từ chối, Đã thanh toán
- Bộ lọc theo học kỳ, tháng, năm

### 2. Tạo yêu cầu chi phí mới
- Form tạo yêu cầu với các trường: Tên yêu cầu, Số tiền, Học kỳ, Mô tả
- Validation đầy đủ cho các trường bắt buộc
- Tự động gán user hiện tại làm người yêu cầu

### 3. Chỉnh sửa yêu cầu chi phí
- Chỉ cho phép chỉnh sửa yêu cầu đang chờ phê duyệt
- Kiểm tra quyền sở hữu (chỉ user tạo mới được chỉnh sửa)
- Cập nhật thông tin: Tên, Số tiền, Học kỳ, Mô tả

### 4. Xóa yêu cầu chi phí
- Chỉ cho phép xóa yêu cầu đang chờ phê duyệt
- Kiểm tra quyền sở hữu
- Xóa đơn lẻ hoặc xóa hàng loạt

### 5. Thống kê
- Tổng số yêu cầu và tổng tiền
- Phân loại theo trạng thái với số lượng và số tiền
- Hiển thị bằng biểu đồ và số liệu

## Phân quyền

Trang sử dụng các phân quyền sau:
- `my_create`: Tạo yêu cầu chi phí
- `my_expenses`: Xem danh sách chi phí của mình
- `my_edit`: Chỉnh sửa chi phí của mình
- `my_delete`: Xóa chi phí của mình

## Routes

### GET Routes
- `/club/my-expenses`: Hiển thị trang Yêu Cầu Thanh Toán

### POST Routes
- `/club/my-expenses`: Tạo yêu cầu chi phí mới

### PUT Routes
- `/club/my-expenses/{expense}`: Cập nhật yêu cầu chi phí

### DELETE Routes
- `/club/my-expenses/{expense}`: Xóa yêu cầu chi phí
- `/club/my-expenses/bulk/destroy`: Xóa nhiều yêu cầu chi phí

## Controller Methods

### ExpenseController
- `myExpenses()`: Hiển thị danh sách yêu cầu chi phí của user
- `myStore()`: Tạo yêu cầu chi phí mới
- `myUpdate()`: Cập nhật yêu cầu chi phí
- `myDestroy()`: Xóa yêu cầu chi phí
- `myBulkDestroy()`: Xóa nhiều yêu cầu chi phí

## View

### File: `resources/views/clubs/expenses/my-expenses.blade.php`
- Layout responsive với sidebar thống kê
- Bảng hiển thị danh sách với checkbox để chọn
- Modal tạo mới và chỉnh sửa
- JavaScript xử lý bulk delete và select all

## Bảo mật

1. **Kiểm tra quyền sở hữu**: Chỉ cho phép user chỉnh sửa/xóa yêu cầu của chính mình
2. **Kiểm tra trạng thái**: Chỉ cho phép chỉnh sửa/xóa yêu cầu đang chờ phê duyệt
3. **Validation**: Kiểm tra đầy đủ dữ liệu đầu vào
4. **Transaction**: Sử dụng database transaction cho các thao tác quan trọng
5. **Middleware**: Sử dụng CheckPermission middleware cho tất cả routes

## Khác biệt với trang Expenses chính

1. **Phạm vi dữ liệu**: Chỉ hiển thị yêu cầu của user hiện tại
2. **Quyền hạn**: Không có quyền phê duyệt/từ chối/đánh dấu đã thanh toán
3. **Giao diện**: Tập trung vào quản lý yêu cầu cá nhân
4. **Routes**: Sử dụng prefix `/my-expenses` để phân biệt

## Sử dụng

1. User đăng nhập vào hệ thống
2. Truy cập trang Yêu Cầu Thanh Toán
3. Xem thống kê và danh sách yêu cầu của mình
4. Tạo yêu cầu mới hoặc chỉnh sửa yêu cầu đang chờ
5. Theo dõi trạng thái yêu cầu qua thống kê 
