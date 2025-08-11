<?php

/**
 * Fixed member import functionality:
 * 1. Updated column mapping to match user's data structure with birth_date and id_card columns
 * 2. Fixed status validation logic by converting validStatuses array to lowercase for proper comparison
 * 3. Updated validation rules and custom messages for new 12-column structure
 */

namespace App\Imports;

use App\Models\User;
use App\Models\UserClubRole;
use App\Models\Major;
use App\Models\Course;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MemberImport implements ToCollection, WithValidation, WithStartRow
{
    use Importable;

    protected $clubId;
    protected $importedCount = 0;
    protected $addedToClubCount = 0;
    protected $errors = [];
    protected $validatedData = []; // Store all validated data before import

    public function __construct($clubId)
    {
        $this->clubId = $clubId;
    }

    /**
     * Bắt đầu lấy dữ liệu từ dòng số 5 (A5) - đây là dòng dữ liệu đầu tiên
     * Row 1: Title, Row 2: Instructions, Row 3: Empty, Row 4: Headers, Row 5+: Data
     */
    public function startRow(): int
    {
        return 5;
    }

    /**
     * Map row data by column position since we're not using WithHeadingRow
     * Columns: Tên thành viên, Email, Giới tính, Số điện thoại, Ngày sinh, CCCD/CMND, Mã sinh viên, Chuyên ngành, Khóa học, Vai trò, Trạng thái, Mật khẩu
     */
    private function mapRowData($row)
    {
        $rowArray = $row->toArray();

        return [
            'name' => $rowArray[0] ?? '',                           // Column A: Tên thành viên
            'email' => $rowArray[1] ?? '',                          // Column B: Email
            'gender' => $rowArray[2] ?? '',                         // Column C: Giới tính
            'phone' => $rowArray[3] ?? '',                          // Column D: Số điện thoại
            'birthday' => $rowArray[4] ?? '',                       // Column E: Ngày sinh
            'identity_card_code' => $rowArray[5] ?? '',             // Column F: CCCD/CMND
            'student_code' => $rowArray[6] ?? '',                   // Column G: Mã sinh viên
            'major_name' => $rowArray[7] ?? '',                     // Column H: Chuyên ngành
            'course_name' => $rowArray[8] ?? '',                    // Column I: Khóa học
            'role_name' => $rowArray[9] ?? '',                      // Column J: Vai trò
            'status' => $rowArray[10] ?? '',                        // Column K: Trạng thái
            'password' => (string)($rowArray[11] ?? '')             // Column L: Mật khẩu (convert to string)
        ];
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Step 1: Validate all data first before any import operation
        $this->validateAllData($collection);

        // Step 2: If there are validation errors, stop the import process
        if (!empty($this->errors)) {
            return; // Don't import anything if there are validation errors
        }

        // Step 3: If all data is valid, perform the import within a database transaction
        DB::beginTransaction();

        try {
            foreach ($this->validatedData as $validData) {
                if ($validData['existing_user']) {
                    // Add existing user to club (check if not already in club)
                    $existingMembership = UserClubRole::where('user_id', $validData['existing_user']->id)
                        ->where('club_id', $this->clubId)
                        ->first();

                    if (!$existingMembership) {
                        UserClubRole::create([
                            'user_id' => $validData['existing_user']->id,
                            'club_id' => $this->clubId,
                            'role_id' => $validData['role_id'],
                            'joined_at' => now(),
                            'status' => $validData['status']
                        ]);

                        $this->addedToClubCount++;
                    }
                } else {
                    // Double-check if user exists before creating (to handle race condition)
                    $email = $validData['user_data']['email'];
                    $existingUser = User::where('email', $email)->first();

                    if ($existingUser) {
                        // User was created by another process, add to club instead
                        $existingMembership = UserClubRole::where('user_id', $existingUser->id)
                            ->where('club_id', $this->clubId)
                            ->first();

                        if (!$existingMembership) {
                            UserClubRole::create([
                                'user_id' => $existingUser->id,
                                'club_id' => $this->clubId,
                                'role_id' => $validData['role_id'],
                                'joined_at' => now(),
                                'status' => $validData['status']
                            ]);

                            $this->addedToClubCount++;
                        }
                    } else {
                        // Use firstOrCreate to handle potential race conditions
                        $newUser = User::firstOrCreate(
                            ['email' => $email], // Search criteria
                            $validData['user_data'] // Data to create if not found
                        );

                        // Add new user to club (check if not already in club)
                        $existingMembership = UserClubRole::where('user_id', $newUser->id)
                            ->where('club_id', $this->clubId)
                            ->first();

                        if (!$existingMembership) {
                            UserClubRole::create([
                                'user_id' => $newUser->id,
                                'club_id' => $this->clubId,
                                'role_id' => $validData['role_id'],
                                'joined_at' => now(),
                                'status' => $validData['status']
                            ]);

                            // Check if this user was actually created (not found)
                            if ($newUser->wasRecentlyCreated) {
                                $this->importedCount++;
                            } else {
                                $this->addedToClubCount++;
                            }
                        }
                    }
                }
            }

            // If we reach here, all imports were successful
            DB::commit();
        } catch (\Exception $e) {
            // If any error occurs during import, rollback all changes
            DB::rollback();
            $this->errors[] = "Lỗi trong quá trình import: " . $e->getMessage();
            $this->importedCount = 0;
            $this->addedToClubCount = 0;
        }
    }

    /**
     * Validate all data before any import operation
     * This ensures we check everything first and only import if all data is valid
     */
    private function validateAllData(Collection $collection)
    {
        $emailsInFile = []; // Track emails within the import file to detect duplicates
        $studentCodesInFile = []; // Track student codes within the import file to detect duplicates

        // Get existing club members to check duplicates
        $existingClubMembers = UserClubRole::where('club_id', $this->clubId)
            ->with('user')
            ->get()
            ->pluck('user.email')
            ->toArray();

        foreach ($collection as $index => $row) {
            $rowNumber = $index + 5; // Actual Excel row number (starting from row 5 for data)

            try {
                // Map the row data by column position
                $mappedRow = $this->mapRowData($row);

                // Skip rows that don't have essential data for import
                // A row is considered empty/invalid if it lacks both name and email,
                // or if only email is provided without a name (common dropdown issue)
                $hasName = !empty(trim($mappedRow['name']));
                $hasEmail = !empty(trim($mappedRow['email']));

                if (!$hasName && !$hasEmail) {
                    // Completely empty row - skip silently
                    continue;
                }

                if (!$hasName && $hasEmail) {
                    // Only email without name - likely dropdown artifact, skip silently
                    continue;
                }

                // Additional check: Skip rows that have name/email but are missing too many required fields
                // This handles cases where dropdowns might create partial data rows
                $essentialFieldsCount = 0;
                $requiredFields = ['name', 'email', 'gender', 'phone', 'student_code', 'major_name', 'course_name', 'role_name', 'status'];

                foreach ($requiredFields as $field) {
                    if (!empty(trim($mappedRow[$field]))) {
                        $essentialFieldsCount++;
                    }
                }

                // If less than 5 essential fields are filled, likely an incomplete/dropdown row
                if ($essentialFieldsCount < 5) {
                    continue;
                }

                // Validate required fields
                if (empty(trim($mappedRow['name']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Tên thành viên không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['email']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Email không được để trống.";
                    continue;
                }

                $email = trim($mappedRow['email']);

                // Check for duplicate emails within the import file
                if (in_array($email, $emailsInFile)) {
                    $this->errors[] = "Dòng {$rowNumber}: Email '{$email}' bị trùng lặp trong file import.";
                    continue;
                }
                $emailsInFile[] = $email;

                // Check if member already exists in club
                if (in_array($email, $existingClubMembers)) {
                    $this->errors[] = "Dòng {$rowNumber}: Thành viên '{$email}' đã tồn tại trong câu lạc bộ.";
                    continue;
                }

                if (empty(trim($mappedRow['gender']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Giới tính không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['phone']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Số điện thoại không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['student_code']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Mã sinh viên không được để trống.";
                    continue;
                }

                $studentCode = trim($mappedRow['student_code']);

                // Check for duplicate student codes within the import file
                if (in_array($studentCode, $studentCodesInFile)) {
                    $this->errors[] = "Dòng {$rowNumber}: Mã sinh viên '{$studentCode}' bị trùng lặp trong file import.";
                    continue;
                }
                $studentCodesInFile[] = $studentCode;

                // Check for duplicate student code in database
                $existingStudentCode = User::where('student_code', $studentCode)->first();
                if ($existingStudentCode) {
                    $this->errors[] = "Dòng {$rowNumber}: Mã sinh viên '{$studentCode}' đã tồn tại trong hệ thống.";
                    continue;
                }

                if (empty(trim($mappedRow['major_name']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Chuyên ngành không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['course_name']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Khóa học không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['role_name']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Vai trò không được để trống.";
                    continue;
                }

                if (empty(trim($mappedRow['status']))) {
                    $this->errors[] = "Dòng {$rowNumber}: Trạng thái không được để trống.";
                    continue;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = "Dòng {$rowNumber}: Email không hợp lệ.";
                    continue;
                }

                // Validate gender
                $validGenders = ['male', 'female', 'nam', 'nữ'];
                $gender = strtolower(trim($mappedRow['gender']));
                if (!in_array($gender, $validGenders)) {
                    $this->errors[] = "Dòng {$rowNumber}: Giới tính không hợp lệ (Nam/Nữ).";
                    continue;
                }

                // Normalize gender
                $normalizedGender = 'male';
                if (in_array($gender, ['nam', 'male'])) {
                    $normalizedGender = 'male';
                } elseif (in_array($gender, ['nữ', 'female'])) {
                    $normalizedGender = 'female';
                }

                // Validate status
                $validStatuses = ['hoạt động', 'không hoạt động', 'active', 'inactive'];
                $status = strtolower(trim($mappedRow['status']));
                if (!in_array($status, $validStatuses)) {
                    $this->errors[] = "Dòng {$rowNumber}: Trạng thái không hợp lệ (Hoạt động/Không hoạt động).";
                    continue;
                }

                // Normalize status
                $normalizedStatus = 'active';
                if (in_array($status, ['không hoạt động', 'inactive'])) {
                    $normalizedStatus = 'inactive';
                }

                // Find Major by name
                $major = null;
                if (!empty(trim($mappedRow['major_name']))) {
                    $major = Major::where('name', 'LIKE', '%' . trim($mappedRow['major_name']) . '%')
                        ->where('status', 'active')
                        ->whereNotNull('parent_id')
                        ->first();

                    if (!$major) {
                        $this->errors[] = "Dòng {$rowNumber}: Không tìm thấy chuyên ngành '{$mappedRow['major_name']}'.";
                        continue;
                    }
                }

                // Find Course by name
                $course = null;
                if (!empty(trim($mappedRow['course_name']))) {
                    $course = Course::where('name', 'LIKE', '%' . trim($mappedRow['course_name']) . '%')
                        ->where('status', 'active')
                        ->first();

                    if (!$course) {
                        $this->errors[] = "Dòng {$rowNumber}: Không tìm thấy khóa học '{$mappedRow['course_name']}'.";
                        continue;
                    }
                }

                // Find Role by name in current club
                $role = null;
                if (!empty(trim($mappedRow['role_name']))) {
                    $role = Role::where('club_id', $this->clubId)
                        ->where('name', 'LIKE', '%' . trim($mappedRow['role_name']) . '%')
                        ->where('status', 'active')
                        ->first();

                    if (!$role) {
                        $this->errors[] = "Dòng {$rowNumber}: Không tìm thấy vai trò '{$mappedRow['role_name']}' trong câu lạc bộ.";
                        continue;
                    }
                }

                // Check if user exists by email
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    // Prepare data for existing user (just add to club)
                    $this->validatedData[] = [
                        'existing_user' => $existingUser,
                        'user_data' => null,
                        'role_id' => $role ? $role->id : null,
                        'status' => $normalizedStatus,
                        'row_number' => $rowNumber
                    ];
                } else {
                    // Prepare data for new user creation
                    $password = !empty(trim($mappedRow['password']))
                        ? trim($mappedRow['password'])
                        : '123456'; // Default password

                    // Handle birthday parsing from Excel
                    $birthday = null;
                    if (!empty(trim($mappedRow['birthday']))) {
                        $birthdayStr = trim($mappedRow['birthday']);

                        // Try parsing different date formats
                        try {
                            // If it's a number (Excel serial date)
                            if (is_numeric($birthdayStr)) {
                                $birthday = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthdayStr)->format('Y-m-d');
                            } else {
                                // Try parsing common date formats
                                $birthday = date('Y-m-d', strtotime($birthdayStr));
                            }
                        } catch (\Exception $e) {
                            // If parsing fails, try to convert manually
                            $birthday = null;
                        }
                    }

                    $userData = [
                        'name' => trim($mappedRow['name']),
                        'email' => $email,
                        'password' => Hash::make($password),
                        'type' => 'branch',
                        'gender' => $normalizedGender,
                        'phone' => trim($mappedRow['phone']),
                        'birthday' => $birthday,
                        'identity_card_code' => !empty(trim($mappedRow['identity_card_code'])) ? trim($mappedRow['identity_card_code']) : null,
                        'student_code' => $studentCode,
                        'major_id' => $major ? $major->id : null,
                        'course_id' => $course ? $course->id : null,
                        'status' => $normalizedStatus
                    ];

                    $this->validatedData[] = [
                        'existing_user' => null,
                        'user_data' => $userData,
                        'role_id' => $role ? $role->id : null,
                        'status' => $normalizedStatus,
                        'row_number' => $rowNumber
                    ];
                }
            } catch (\Exception $e) {
                $this->errors[] = "Dòng {$rowNumber}: Lỗi không xác định - " . $e->getMessage();
            }
        }
    }

    /**
     * Validation rules for each row - using column positions (0-11)
     */
    public function rules(): array
    {
        return [
            '0' => 'nullable|string|max:255',      // Column A: Tên thành viên
            '1' => 'nullable|email|max:255',       // Column B: Email
            '2' => 'nullable|string|max:50',       // Column C: Giới tính
            '3' => 'nullable|string|max:20',       // Column D: Số điện thoại
            '4' => 'nullable|date',                // Column E: Ngày sinh
            '5' => 'nullable|string|max:20',       // Column F: CCCD/CMND
            '6' => 'nullable|string|max:50',       // Column G: Mã sinh viên
            '7' => 'nullable|string|max:255',      // Column H: Chuyên ngành
            '8' => 'nullable|string|max:255',      // Column I: Khóa học
            '9' => 'nullable|string|max:255',      // Column J: Vai trò
            '10' => 'nullable|string|max:50',      // Column K: Trạng thái
            '11' => 'nullable|max:255'             // Column L: Mật khẩu (cho phép cả string và số)
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            '0.string' => 'Tên thành viên phải là chuỗi ký tự.',
            '0.max' => 'Tên thành viên không được vượt quá 255 ký tự.',
            '1.email' => 'Email không hợp lệ.',
            '1.max' => 'Email không được vượt quá 255 ký tự.',
            '2.string' => 'Giới tính phải là chuỗi ký tự.',
            '2.max' => 'Giới tính không được vượt quá 50 ký tự.',
            '3.string' => 'Số điện thoại phải là chuỗi ký tự.',
            '3.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            '4.date' => 'Ngày sinh phải là ngày hợp lệ.',
            '5.string' => 'CCCD/CMND phải là chuỗi ký tự.',
            '5.max' => 'CCCD/CMND không được vượt quá 20 ký tự.',
            '6.string' => 'Mã sinh viên phải là chuỗi ký tự.',
            '6.max' => 'Mã sinh viên không được vượt quá 50 ký tự.',
            '7.string' => 'Tên chuyên ngành phải là chuỗi ký tự.',
            '7.max' => 'Tên chuyên ngành không được vượt quá 255 ký tự.',
            '8.string' => 'Tên khóa học phải là chuỗi ký tự.',
            '8.max' => 'Tên khóa học không được vượt quá 255 ký tự.',
            '9.string' => 'Tên vai trò phải là chuỗi ký tự.',
            '9.max' => 'Tên vai trò không được vượt quá 255 ký tự.',
            '10.string' => 'Trạng thái phải là chuỗi ký tự.',
            '10.max' => 'Trạng thái không được vượt quá 50 ký tự.',
            '11.max' => 'Mật khẩu không được vượt quá 255 ký tự.'
        ];
    }

    /**
     * Get number of imported records (new users created)
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Get number of existing users added to club
     */
    public function getAddedToClubCount()
    {
        return $this->addedToClubCount;
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
