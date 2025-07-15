<!--
README.md - ĐÃ VIẾT LẠI: Giới thiệu hệ thống quản lý câu lạc bộ, sự kiện, thành viên, tài chính và học thuật cho trường học hoặc tổ chức.
Tính năng: Quản lý CLB, thành viên, sự kiện, kế hoạch, tài chính, học tập, chấm ngôn, phân quyền, check-in QR, xuất Excel, v.v.
Đối tượng sử dụng: Quản trị viên, quản lý, thành viên CLB, sinh viên, cán bộ tổ chức.
-->

# HỆ THỐNG QUẢN LÝ CÂU LẠC BỘ & SỰ KIỆN TRƯỜNG HỌC

## Giới thiệu
Hệ thống này là một nền tảng quản lý toàn diện dành cho các trường học, tổ chức giáo dục hoặc các đơn vị có nhiều câu lạc bộ, sự kiện và thành viên. Được xây dựng trên nền tảng Laravel, hệ thống hỗ trợ quản lý đa chiều các hoạt động học thuật, sự kiện, tài chính, nhân sự và phân quyền linh hoạt.

## Đối tượng sử dụng
- **Quản trị viên hệ thống**: Quản lý tổng thể, phân quyền, cấu hình hệ thống.
- **Quản lý/Chủ nhiệm CLB**: Quản lý thành viên, sự kiện, tài chính, kế hoạch hoạt động của từng CLB.
- **Thành viên CLB/Sinh viên**: Tham gia sự kiện, cập nhật hồ sơ cá nhân, theo dõi hoạt động, nhận giải thưởng.
- **Cán bộ tổ chức**: Theo dõi, phê duyệt, hỗ trợ các hoạt động của CLB và sinh viên.

## Tính năng chính
- **Quản lý Câu lạc bộ**: Tạo, chỉnh sửa, xóa CLB; quản lý vai trò, thành viên, giải thưởng, hóa đơn, sự kiện.
- **Quản lý Thành viên**: Hồ sơ cá nhân, thông tin học tập, vai trò, điểm danh sự kiện, nhận giải thưởng.
- **Quản lý Sự kiện & Kế hoạch**: Tạo sự kiện, lập kế hoạch chi tiết (mục tiêu, hành động, ngân sách, vật dụng, timeline), xuất Excel, quản lý hình ảnh, check-in QR code.
- **Quản lý Tài chính**: Hóa đơn, chi phí, giao dịch, phê duyệt, thống kê thu chi, quản lý trạng thái thanh toán.
- **Quản lý Học tập**: Chuyên ngành, khóa học, lớp học, học kỳ, phân công thành viên.
- **Quản lý Châm ngôn**: Thêm, sửa, xóa các câu châm ngôn truyền cảm hứng.
- **Phân quyền & Bảo mật**: Hệ thống vai trò, quyền hạn chi tiết cho từng mô-đun, kiểm soát truy cập.
- **Dashboard & Báo cáo**: Thống kê tổng quan, biểu đồ, xuất dữ liệu.
- **Tích hợp**: Xuất Excel, check-in QR code, thông báo Flasher.

## Công nghệ sử dụng
- **Laravel Framework** (PHP)
- **MySQL** (hoặc các hệ quản trị CSDL tương thích)
- **HTML5, CSS3, JavaScript** (Giao diện hiện đại, responsive)
- **Các thư viện hỗ trợ**: Maatwebsite Excel, Flasher, Simple QrCode, v.v.

## Hướng dẫn cài đặt & sử dụng
1. Clone source code về máy chủ.
2. Cài đặt các package PHP bằng Composer.
3. Tạo database và cấu hình `.env`.
4. Chạy migrate và seed dữ liệu mẫu:
   ```bash
   php artisan migrate --seed
   ```
5. Khởi động server:
   ```bash
   php artisan serve
   ```
6. Đăng nhập bằng tài khoản mẫu (admin@gmail.com / 12345678) hoặc tạo mới.

## Đóng góp & Giấy phép
- Mọi đóng góp xin gửi pull request hoặc liên hệ quản trị viên hệ thống.
- Hệ thống sử dụng giấy phép MIT.
