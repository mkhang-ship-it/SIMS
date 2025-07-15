<?php
// Updated: Replaced Route::resource() with individual routes to add CheckPermission middleware for each action
// Each route now has specific middleware for view, create, edit, delete permissions
// Added Major routes with parent-child relationship support
// Updated root route to dynamically redirect to backend.dashboard or backend.club.dashboard based on user permissions
// Added AdminEventController routes for global event management with view and approve permissions
// Merged QR scanner and check-in pages into one - removed separate checkin route, added API endpoint for event info
// Added Event Images routes for club event image management with upload, view, download, single delete, and bulk delete functionality (using separate route path to avoid conflicts)
// Added Event Plans routes for individual event planning management with detailed view and update functionality
// Added Event Action Plans routes for detailed action plan management with CRUD operations and timeline management
// Added Event Plan Budget Items routes for budget cost management with CRUD operations and financial calculations
// Added Event Plan Equipment routes for equipment management with CRUD operations
// Added Event Plan Timeline routes for timeline management with CRUD operations and drag/drop ordering support
// Added Excel export route for event plans to export comprehensive event plan information to Excel format

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\EventController as AdminEventController;
use App\Http\Controllers\Club\RoleController as ClubRoleController;
use App\Http\Controllers\Club\DashboardController as ClubDashboardController;
use App\Http\Controllers\Club\MemberController as ClubMemberController;
use App\Http\Controllers\Club\AwardController as ClubAwardController;
use App\Http\Controllers\Club\EventController as ClubEventController;
use App\Http\Controllers\Club\EventPlanController as ClubEventPlanController;
use App\Http\Controllers\Club\CheckinController as ClubCheckinController;
use App\Http\Controllers\Club\EventImageController as ClubEventImageController;
use App\Http\Controllers\Club\InvoiceController as ClubInvoiceController;
use App\Http\Controllers\Club\ExpenseController as ClubExpenseController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\EventImageController;
use App\Http\Controllers\EventPlanController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Club\MyProfileController as ClubMyProfileController;
use App\Http\Controllers\Club\SettingController as ClubSettingController;
use App\Models\User;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if ($user->club_id) {
        return redirect()->route('backend.club.dashboard');
    } else {
        return redirect()->route('backend.dashboard');
    }
});


// Authentication routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::name('backend.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('CheckPermission:dashboard,view,central');

        Route::get('/my-profile', [MyProfileController::class, 'index'])->name('my-profile.index')->middleware('CheckPermission:user_management,my_profile,central');
        Route::post('/my-profile/update', [MyProfileController::class, 'update'])->name('my-profile.update')->middleware('CheckPermission:user_management,my_profile,central');
        Route::post('/my-profile/update-password', [MyProfileController::class, 'updatePassword'])->name('my-profile.update-password')->middleware('CheckPermission:user_management,my_profile,central');

        // Courses routes
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index')->middleware('CheckPermission:course_management,view,central');
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create')->middleware('CheckPermission:course_management,create,central');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store')->middleware('CheckPermission:course_management,create,central');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit')->middleware('CheckPermission:course_management,edit,central');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update')->middleware('CheckPermission:course_management,edit,central');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy')->middleware('CheckPermission:course_management,delete,central');

        // Semesters routes
        Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index')->middleware('CheckPermission:semester_management,view,central');
        Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create')->middleware('CheckPermission:semester_management,create,central');
        Route::post('/semesters', [SemesterController::class, 'store'])->name('semesters.store')->middleware('CheckPermission:semester_management,create,central');
        Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit')->middleware('CheckPermission:semester_management,edit,central');
        Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update')->middleware('CheckPermission:semester_management,edit,central');
        Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])->name('semesters.destroy')->middleware('CheckPermission:semester_management,delete,central');

        // Clubs routes
        Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index')->middleware('CheckPermission:club_management,view,central');
        Route::get('/clubs/create', [ClubController::class, 'create'])->name('clubs.create')->middleware('CheckPermission:club_management,create,central');
        Route::post('/clubs', [ClubController::class, 'store'])->name('clubs.store')->middleware('CheckPermission:club_management,create,central');
        Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])->name('clubs.edit')->middleware('CheckPermission:club_management,edit,central');
        Route::put('/clubs/{club}', [ClubController::class, 'update'])->name('clubs.update')->middleware('CheckPermission:club_management,edit,central');
        Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy')->middleware('CheckPermission:club_management,delete,central');
        Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show')->middleware('CheckPermission:club_management,view,central');

        // Slots routes
        Route::get('/slots', [SlotController::class, 'index'])->name('slots.index')->middleware('CheckPermission:slot_management,view,central');
        Route::get('/slots/create', [SlotController::class, 'create'])->name('slots.create')->middleware('CheckPermission:slot_management,create,central');
        Route::post('/slots', [SlotController::class, 'store'])->name('slots.store')->middleware('CheckPermission:slot_management,create,central');
        Route::get('/slots/{slot}/edit', [SlotController::class, 'edit'])->name('slots.edit')->middleware('CheckPermission:slot_management,edit,central');
        Route::put('/slots/{slot}', [SlotController::class, 'update'])->name('slots.update')->middleware('CheckPermission:slot_management,edit,central');
        Route::delete('/slots/{slot}', [SlotController::class, 'destroy'])->name('slots.destroy')->middleware('CheckPermission:slot_management,delete,central');

        // Classrooms routes
        Route::get('/classrooms', [ClassRoomController::class, 'index'])->name('classrooms.index')->middleware('CheckPermission:classroom_management,view,central');
        Route::get('/classrooms/create', [ClassRoomController::class, 'create'])->name('classrooms.create')->middleware('CheckPermission:classroom_management,create,central');
        Route::post('/classrooms', [ClassRoomController::class, 'store'])->name('classrooms.store')->middleware('CheckPermission:classroom_management,create,central');
        Route::get('/classrooms/{classroom}/edit', [ClassRoomController::class, 'edit'])->name('classrooms.edit')->middleware('CheckPermission:classroom_management,edit,central');
        Route::put('/classrooms/{classroom}', [ClassRoomController::class, 'update'])->name('classrooms.update')->middleware('CheckPermission:classroom_management,edit,central');
        Route::delete('/classrooms/{classroom}', [ClassRoomController::class, 'destroy'])->name('classrooms.destroy')->middleware('CheckPermission:classroom_management,delete,central');

        // Roles routes
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('CheckPermission:role_management,view,central');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('CheckPermission:role_management,create,central');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('CheckPermission:role_management,create,central');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('CheckPermission:role_management,edit,central');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('CheckPermission:role_management,edit,central');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('CheckPermission:role_management,delete,central');

        // Majors routes
        Route::get('/majors', [MajorController::class, 'index'])->name('majors.index')->middleware('CheckPermission:major_management,view,central');
        Route::get('/majors/create', [MajorController::class, 'create'])->name('majors.create')->middleware('CheckPermission:major_management,create,central');
        Route::post('/majors', [MajorController::class, 'store'])->name('majors.store')->middleware('CheckPermission:major_management,create,central');
        Route::get('/majors/{major}/edit', [MajorController::class, 'edit'])->name('majors.edit')->middleware('CheckPermission:major_management,edit,central');
        Route::put('/majors/{major}', [MajorController::class, 'update'])->name('majors.update')->middleware('CheckPermission:major_management,edit,central');
        Route::delete('/majors/{major}', [MajorController::class, 'destroy'])->name('majors.destroy')->middleware('CheckPermission:major_management,delete,central');

        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index')->middleware('CheckPermission:event_management,view,central');
        Route::get('/events/timetable/show', [AdminEventController::class, 'timetable'])->name('events.timetable')->middleware('CheckPermission:event_management,view,central');
        Route::put('/events/{event}/approve', [AdminEventController::class, 'approve'])->name('events.approve')->middleware('CheckPermission:event_management,approve,central');
        Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show')->middleware('CheckPermission:event_management,view,central');
        Route::get('/events/gallery/view', [EventImageController::class, 'index'])->name('events.images.gallery-view')->middleware('CheckPermission:event_management,view_image,central');
        Route::get('/events/{event}/images/{image}/download', [EventImageController::class, 'download'])->name('events.images.download.admin')->middleware('CheckPermission:event_management,view_image,central');
        Route::get('/events/plans/index', [EventPlanController::class, 'index'])->name('events.event-plans.index')->middleware('CheckPermission:event_management,view_plan,central');
        Route::get('/events/plans/{event}', [EventPlanController::class, 'show'])->name('events.event-plans.show')->middleware('CheckPermission:event_management,view_plan,central');
        Route::get('/events/plans/{event}/export/excel', [EventPlanController::class, 'exportExcel'])->name('events.event-plans.export.excel')->middleware('CheckPermission:event_management,view_plan,central');

        // Quotes routes
        Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index')->middleware('CheckPermission:quote_management,view,central');
        Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store')->middleware('CheckPermission:quote_management,create,central');
        Route::get('/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit')->middleware('CheckPermission:quote_management,edit,central');
        Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update')->middleware('CheckPermission:quote_management,edit,central');
        Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy')->middleware('CheckPermission:quote_management,delete,central');

        // Members routes
        Route::get('/members', [MemberController::class, 'index'])->name('members.index')->middleware('CheckPermission:member_management,view,central');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store')->middleware('CheckPermission:member_management,create,central');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update')->middleware('CheckPermission:member_management,edit,central');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy')->middleware('CheckPermission:member_management,delete,central');
        Route::post('/members/assign-to-club', [MemberController::class, 'assignToClub'])->name('members.assign-to-club')->middleware('CheckPermission:member_management,create,central');
        Route::post('/members/remove-from-club', [MemberController::class, 'removeFromClub'])->name('members.remove-from-club')->middleware('CheckPermission:member_management,delete,central');
        Route::post('/members/update-club-role', [MemberController::class, 'updateClubRole'])->name('members.update-club-role')->middleware('CheckPermission:member_management,edit,central');
        Route::get('/members/get-club-roles', [MemberController::class, 'getClubRoles'])->name('members.get-club-roles')->middleware('CheckPermission:member_management,view,central');
        Route::get('/members/check-email', [MemberController::class, 'checkUserEmail'])->name('members.check-email')->middleware('CheckPermission:member_management,view,central');
    });
});


Route::middleware(['auth'])->prefix('club')->group(function () {
    Route::name('backend.club.')->group(function () {
        Route::get('/dashboard', [ClubDashboardController::class, 'index'])->name('dashboard')->middleware('CheckPermission:dashboard,view,branch');

        Route::get('/my-profile', [ClubMyProfileController::class, 'index'])->name('my-profile.index')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update', [ClubMyProfileController::class, 'update'])->name('my-profile.update')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-password', [ClubMyProfileController::class, 'updatePassword'])->name('my-profile.update-password')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-bank-account', [ClubMyProfileController::class, 'updateBankAccount'])->name('my-profile.update-bank-account')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-address', [ClubMyProfileController::class, 'updateAddress'])->name('my-profile.update-address')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::get('/my-profile/get-communes', [ClubMyProfileController::class, 'getCommunes'])->name('my-profile.get-communes')->middleware('CheckPermission:member_management,my_profile,branch');

        // Roles routes
        Route::get('/roles', [ClubRoleController::class, 'index'])->name('roles.index')->middleware('CheckPermission:role_management,view,branch');
        Route::get('/roles/create', [ClubRoleController::class, 'create'])->name('roles.create')->middleware('CheckPermission:role_management,create,branch');
        Route::post('/roles', [ClubRoleController::class, 'store'])->name('roles.store')->middleware('CheckPermission:role_management,create,branch');
        Route::get('/roles/{role}/edit', [ClubRoleController::class, 'edit'])->name('roles.edit')->middleware('CheckPermission:role_management,edit,branch');
        Route::put('/roles/{role}', [ClubRoleController::class, 'update'])->name('roles.update')->middleware('CheckPermission:role_management,edit,branch');
        Route::delete('/roles/{role}', [ClubRoleController::class, 'destroy'])->name('roles.destroy')->middleware('CheckPermission:role_management,delete,branch');

        // Members routes
        Route::get('/members', [ClubMemberController::class, 'index'])->name('members.index')->middleware('CheckPermission:member_management,view,branch');
        Route::post('/members', [ClubMemberController::class, 'store'])->name('members.store')->middleware('CheckPermission:member_management,create,branch');
        Route::get('/members/{member}', [ClubMemberController::class, 'show'])->name('members.show')->middleware('CheckPermission:member_management,view,branch');
        Route::put('/members/{member}', [ClubMemberController::class, 'update'])->name('members.update')->middleware('CheckPermission:member_management,edit,branch');
        Route::delete('/members/{member}', [ClubMemberController::class, 'destroy'])->name('members.destroy')->middleware('CheckPermission:member_management,delete,branch');

        // Awards routes
        Route::get('/awards', [ClubAwardController::class, 'index'])->name('awards.index')->middleware('CheckPermission:award_management,view,branch');
        Route::post('/awards', [ClubAwardController::class, 'store'])->name('awards.store')->middleware('CheckPermission:award_management,create,branch');
        Route::get('/awards/{award}', [ClubAwardController::class, 'show'])->name('awards.show')->middleware('CheckPermission:award_management,view,branch');
        Route::put('/awards/{award}', [ClubAwardController::class, 'update'])->name('awards.update')->middleware('CheckPermission:award_management,edit,branch');
        Route::delete('/awards/{award}', [ClubAwardController::class, 'destroy'])->name('awards.destroy')->middleware('CheckPermission:award_management,delete,branch');

        // Award Recipients routes (integrated into AwardController)
        Route::post('/awards/{award}/recipients', [ClubAwardController::class, 'storeRecipient'])->name('awards.recipients.store')->middleware('CheckPermission:award_management,create,branch');
        Route::put('/awards/{award}/recipients/{recipient}', [ClubAwardController::class, 'updateRecipient'])->name('awards.recipients.update')->middleware('CheckPermission:award_management,edit,branch');
        Route::delete('/awards/{award}/recipients/{recipient}', [ClubAwardController::class, 'destroyRecipient'])->name('awards.recipients.destroy')->middleware('CheckPermission:award_management,delete,branch');

        // Events routes
        Route::get('/events', [ClubEventController::class, 'index'])->name('events.index')->middleware('CheckPermission:event_management,view,branch');
        Route::post('/events', [ClubEventController::class, 'store'])->name('events.store')->middleware('CheckPermission:event_management,create,branch');
        Route::get('/events/{event}', [ClubEventController::class, 'show'])->name('events.show')->middleware('CheckPermission:event_management,view,branch');
        Route::put('/events/{event}', [ClubEventController::class, 'update'])->name('events.update')->middleware('CheckPermission:event_management,edit,branch');
        Route::delete('/events/{event}', [ClubEventController::class, 'destroy'])->name('events.destroy')->middleware('CheckPermission:event_management,delete,branch');

        // QR Code route for events
        Route::get('/events/{event}/qrcode', [ClubEventController::class, 'generateQrCode'])->name('events.qrcode')->middleware('CheckPermission:event_management,view,branch');

        Route::get('/events/checkin/scanner', [ClubCheckinController::class, 'scanner'])->name('events.checkin.scanner')->middleware('CheckPermission:event_management,checkin,branch');
        Route::get('/events/checkin/event-info/{qrHash}', [ClubCheckinController::class, 'getEventInfo'])->name('events.checkin.event.info')->middleware('CheckPermission:event_management,checkin,branch');
        Route::post('/events/checkin/{qrHash}', [ClubCheckinController::class, 'checkin'])->name('events.checkin.process')->middleware('CheckPermission:event_management,checkin,branch');
        Route::get('/events/participated/my', [ClubCheckinController::class, 'myParticipatedEvents'])->name('events.my-participated')->middleware('CheckPermission:event_management,my_checkin,branch');

        // Timetable route for events
        Route::get('/events/timetable/show', [ClubEventController::class, 'timetable'])->name('events.timetable')->middleware('CheckPermission:event_management,view_timetable,branch');

        // Export Excel route for events
        Route::get('/events/{event}/export/excel', [ClubEventPlanController::class, 'exportExcel'])->name('events.export.excel')->middleware('CheckPermission:event_plan_management,view,branch');

        // Event Plans routes
        Route::get('/event-plans/{event}', [ClubEventPlanController::class, 'show'])->name('event-plans.show')->middleware('CheckPermission:event_plan_management,view,branch');
        Route::put('/event-plans/{event}', [ClubEventPlanController::class, 'update'])->name('event-plans.update')->middleware('CheckPermission:event_plan_management,edit,branch');

        // Event Plan Objectives routes
        Route::post('/event-plans/{event}/objectives', [ClubEventPlanController::class, 'storeObjective'])->name('event-plans.objectives.store')->middleware('CheckPermission:event_plan_management,create,branch');
        Route::put('/event-plans/{event}/objectives/{objective}', [ClubEventPlanController::class, 'updateObjective'])->name('event-plans.objectives.update')->middleware('CheckPermission:event_plan_management,edit,branch');
        Route::delete('/event-plans/{event}/objectives/{objective}', [ClubEventPlanController::class, 'destroyObjective'])->name('event-plans.objectives.destroy')->middleware('CheckPermission:event_plan_management,delete,branch');

        // Event Plan Action Plans routes
        Route::post('/event-plans/{event}/action-plans', [ClubEventPlanController::class, 'storeActionPlan'])->name('event-plans.action-plans.store')->middleware('CheckPermission:event_plan_management,create,branch');
        Route::put('/event-plans/{event}/action-plans/{actionPlan}', [ClubEventPlanController::class, 'updateActionPlan'])->name('event-plans.action-plans.update')->middleware('CheckPermission:event_plan_management,edit,branch');
        Route::delete('/event-plans/{event}/action-plans/{actionPlan}', [ClubEventPlanController::class, 'destroyActionPlan'])->name('event-plans.action-plans.destroy')->middleware('CheckPermission:event_plan_management,delete,branch');

        // Event Plan Budget Items routes
        Route::post('/event-plans/{event}/budget-items', [ClubEventPlanController::class, 'storeBudgetItem'])->name('event-plans.budget-items.store')->middleware('CheckPermission:event_plan_management,create,branch');
        Route::put('/event-plans/{event}/budget-items/{budgetItem}', [ClubEventPlanController::class, 'updateBudgetItem'])->name('event-plans.budget-items.update')->middleware('CheckPermission:event_plan_management,edit,branch');
        Route::delete('/event-plans/{event}/budget-items/{budgetItem}', [ClubEventPlanController::class, 'destroyBudgetItem'])->name('event-plans.budget-items.destroy')->middleware('CheckPermission:event_plan_management,delete,branch');

        // Event Plan Equipment routes
        Route::post('/event-plans/{event}/equipment', [ClubEventPlanController::class, 'storeEquipment'])->name('event-plans.equipment.store')->middleware('CheckPermission:event_plan_management,create,branch');
        Route::put('/event-plans/{event}/equipment/{equipment}', [ClubEventPlanController::class, 'updateEquipment'])->name('event-plans.equipment.update')->middleware('CheckPermission:event_plan_management,edit,branch');
        Route::delete('/event-plans/{event}/equipment/{equipment}', [ClubEventPlanController::class, 'destroyEquipment'])->name('event-plans.equipment.destroy')->middleware('CheckPermission:event_plan_management,delete,branch');

        // Event Plan Timeline routes
        Route::post('/event-plans/{event}/timeline', [ClubEventPlanController::class, 'storeTimeline'])->name('event-plans.timeline.store')->middleware('CheckPermission:event_plan_management,create,branch');
        Route::put('/event-plans/{event}/timeline/{timeline}', [ClubEventPlanController::class, 'updateTimeline'])->name('event-plans.timeline.update')->middleware('CheckPermission:event_plan_management,edit,branch');
        Route::delete('/event-plans/{event}/timeline/{timeline}', [ClubEventPlanController::class, 'destroyTimeline'])->name('event-plans.timeline.destroy')->middleware('CheckPermission:event_plan_management,delete,branch');
        Route::put('/event-plans/{event}/timeline-order', [ClubEventPlanController::class, 'updateTimelineOrder'])->name('event-plans.timeline.order')->middleware('CheckPermission:event_plan_management,edit,branch');

        // Event Images route
        Route::get('/events/{event}/images', [ClubEventImageController::class, 'index'])->name('events.images.index')->middleware('CheckPermission:event_image_management,view,branch');
        Route::post('/events/{event}/images', [ClubEventImageController::class, 'store'])->name('events.images.store')->middleware('CheckPermission:event_image_management,create,branch');
        Route::delete('/events/{event}/images/bulk-delete', [ClubEventImageController::class, 'bulkDelete'])->name('events.images.bulk-delete')->middleware('CheckPermission:event_image_management,delete,branch');
        Route::get('/events/{event}/images/{image}/download', [ClubEventImageController::class, 'download'])->name('events.images.download')->middleware('CheckPermission:event_image_management,view,branch');
        Route::delete('/events/{event}/images/{image}', [ClubEventImageController::class, 'destroy'])->name('events.images.destroy')->middleware('CheckPermission:event_image_management,delete,branch');
        Route::get('/events/gallery/view', [ClubEventImageController::class, 'galleryView'])->name('events.images.gallery-view')->middleware('CheckPermission:event_image_management,view_image,branch');

        // Invoices routes
        Route::get('/invoices', [ClubInvoiceController::class, 'index'])->name('invoices.index')->middleware('CheckPermission:invoice_management,view,branch');
        Route::post('/invoices', [ClubInvoiceController::class, 'store'])->name('invoices.store')->middleware('CheckPermission:invoice_management,create,branch');
        Route::get('/invoices/{invoice}', [ClubInvoiceController::class, 'show'])->name('invoices.show')->middleware('CheckPermission:invoice_management,view,branch');
        Route::put('/invoices/{invoice}', [ClubInvoiceController::class, 'update'])->name('invoices.update')->middleware('CheckPermission:invoice_management,edit,branch');
        Route::delete('/invoices/{invoice}', [ClubInvoiceController::class, 'destroy'])->name('invoices.destroy')->middleware('CheckPermission:invoice_management,delete,branch');
        Route::delete('/invoices/bulk-destroy', [ClubInvoiceController::class, 'bulkDestroy'])->name('invoices.bulk-destroy')->middleware('CheckPermission:invoice_management,delete,branch');
        // Export PDF route
        Route::get('/invoices/{invoice}/export/pdf', [ClubInvoiceController::class, 'exportPdf'])->name('invoices.export.pdf')->middleware('CheckPermission:invoice_management,view,branch');

        // Mark as paid route
        Route::post('/invoices/{invoice}/transactions/mark-as-paid', [ClubInvoiceController::class, 'markAsPaid'])->name('invoices.transactions.mark-as-paid')->middleware('CheckPermission:invoice_management,mark_as_paid,branch');

        // Overdue status update route
        Route::put('/invoices/update-overdue-status', [ClubInvoiceController::class, 'updateOverdueStatus'])->name('invoices.update-overdue-status')->middleware('CheckPermission:invoice_management,edit,branch');

        // My Invoices route for members
        Route::get('/my-invoices', [ClubInvoiceController::class, 'myInvoices'])->name('invoices.my-invoices')->middleware('CheckPermission:invoice_management,my_invoices,branch');

        // Expenses routes
        Route::get('/expenses', [ClubExpenseController::class, 'index'])->name('expenses.index')->middleware('CheckPermission:expense_management,view,branch');
        Route::post('/expenses', [ClubExpenseController::class, 'store'])->name('expenses.store')->middleware('CheckPermission:expense_management,create,branch');
        Route::put('/expenses/{expense}', [ClubExpenseController::class, 'update'])->name('expenses.update')->middleware('CheckPermission:expense_management,edit,branch');
        Route::delete('/expenses/{expense}', [ClubExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('CheckPermission:expense_management,delete,branch');
        Route::delete('/expenses/bulk/destroy', [ClubExpenseController::class, 'bulkDestroy'])->name('expenses.bulk-destroy')->middleware('CheckPermission:expense_management,delete,branch');

        // Expense approval routes
        Route::put('/expenses/{expense}/approve', [ClubExpenseController::class, 'approve'])->name('expenses.approve')->middleware('CheckPermission:expense_management,approve,branch');
        Route::put('/expenses/{expense}/reject', [ClubExpenseController::class, 'reject'])->name('expenses.reject')->middleware('CheckPermission:expense_management,reject,branch');
        Route::put('/expenses/{expense}/mark-as-paid', [ClubExpenseController::class, 'markAsPaid'])->name('expenses.mark-as-paid')->middleware('CheckPermission:expense_management,edit,branch');

        // My Expenses routes (Yêu Cầu Thanh Toán cho user)
        Route::get('/my-expenses', [ClubExpenseController::class, 'myExpenses'])->name('expenses.my-expenses')->middleware('CheckPermission:expense_management,my_expenses,branch');
        Route::post('/my-expenses', [ClubExpenseController::class, 'myStore'])->name('expenses.my-store')->middleware('CheckPermission:expense_management,my_create,branch');
        Route::put('/my-expenses/{expense}', [ClubExpenseController::class, 'myUpdate'])->name('expenses.my-update')->middleware('CheckPermission:expense_management,my_edit,branch');
        Route::delete('/my-expenses/{expense}', [ClubExpenseController::class, 'myDestroy'])->name('expenses.my-destroy')->middleware('CheckPermission:expense_management,my_delete,branch');
        Route::delete('/my-expenses/bulk/destroy', [ClubExpenseController::class, 'myBulkDestroy'])->name('expenses.my-bulk-destroy')->middleware('CheckPermission:expense_management,my_delete,branch');

        // Settings routes
        Route::get('/settings', [ClubSettingController::class, 'index'])->name('settings.index')->middleware('CheckPermission:setting_management,view,branch');
        // Update general settings
        Route::post('/settings/general', [ClubSettingController::class, 'updateGeneralSettings'])->name('settings.general.update')->middleware('CheckPermission:setting_management,general,branch');
        // Update word commitment template
        Route::post('/settings/word-commitment-template', [ClubSettingController::class, 'updateWordCommitmentTemplate'])->name('settings.word-commitment-template.update')->middleware('CheckPermission:setting_management,word_template,branch');
        // Download word commitment template
        Route::get('/settings/word-commitment-template/download', [ClubSettingController::class, 'downloadWordCommitmentTemplate'])->name('settings.word-commitment-template.download')->middleware('CheckPermission:setting_management,word_template,branch');
        // Bank settings routes
        Route::get('/settings/banks', [ClubSettingController::class, 'getBanksFromAPI'])->name('settings.banks.api')->middleware('CheckPermission:setting_management,payment,branch');
        Route::post('/settings/bank', [ClubSettingController::class, 'updateBankSettings'])->name('settings.bank.update')->middleware('CheckPermission:setting_management,payment,branch');
    });
});

// [AI CHANGE 2024-07-14] Route đổi club mặc định cho user
Route::post('/switch-default-club', [App\Http\Controllers\SwitchClubController::class, 'switch'])->name('switch.default.club')->middleware('auth');
