<?php

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
use App\Http\Controllers\Club\DisciplineController as ClubDisciplineController;
use App\Http\Controllers\Club\DisciplineRecordController as ClubDisciplineRecordController;
use App\Http\Controllers\Club\MyPointsController as ClubMyPointsController;
use App\Http\Controllers\DisciplineRecordController;
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
use App\Http\Controllers\Club\NotificationController as ClubNotificationController;
use App\Http\Controllers\Club\LuckyDrawController as ClubLuckyDrawController;
use App\Http\Controllers\Club\PrizeController as ClubPrizeController;
use App\Http\Controllers\Club\FeatureRequestController as ClubFeatureRequestController;
use App\Http\Controllers\LuckyDrawPublicController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SwitchClubController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FeatureRequestController;
use App\Http\Controllers\LuckyDrawController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\Club\SessionController as ClubSessionController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\LogController;

// Bypass route for maintenance mode - handled entirely by middleware
Route::middleware('logsystem')->get('/bypass/{token}', function ($token) {
    // This route is handled entirely by the maintenance middleware
    // The middleware will validate the token and set session/cookie
    // Then redirect based on authentication status
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if ($user->club_id) {
        return redirect()->route('backend.club.dashboard');
    } else {
        return redirect()->route('backend.dashboard');
    }
})->where('token', '[a-zA-Z0-9_]+');

Route::middleware('logsystem')->get('/', function () {
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
Route::middleware(['guest', 'logsystem'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // GitHub OAuth routes
    Route::get('/auth/github', [LoginController::class, 'redirectToGitHub'])->name('auth.github');
    Route::get('/auth/github/callback', [LoginController::class, 'handleGitHubCallback'])->name('auth.github.callback');

    // Microsoft OAuth routes
    Route::get('/auth/microsoft', [LoginController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [LoginController::class, 'handleMicrosoftCallback'])->name('auth.microsoft.callback');
});

Route::middleware(['auth', 'logsystem'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::name('backend.')->group(function () {
        // Impersonation routes
        Route::post('/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
        Route::post('/impersonate/stop/end', [ImpersonationController::class, 'stop'])->name('impersonate.stop');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('CheckPermission:dashboard,view,central');

        Route::get('/my-profile', [MyProfileController::class, 'index'])->name('my-profile.index')->middleware('CheckPermission:user_management,my_profile,central');
        Route::post('/my-profile/update', [MyProfileController::class, 'update'])->name('my-profile.update')->middleware('CheckPermission:user_management,my_profile,central');
        Route::post('/my-profile/update-password', [MyProfileController::class, 'updatePassword'])->name('my-profile.update-password')->middleware('CheckPermission:user_management,my_profile,central');
        Route::delete('/my-profile/destroy-session/{sessionId}', [MyProfileController::class, 'destroySession'])->name('my-profile.destroy-session')->middleware('CheckPermission:user_management,my_profile,central');
        Route::delete('/my-profile/destroy-all-sessions', [MyProfileController::class, 'destroyAllSessions'])->name('my-profile.destroy-all-sessions')->middleware('CheckPermission:user_management,my_profile,central');


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
        Route::get('/clubs/get/communes', [ClubController::class, 'getCommunes'])->name('clubs.get-communes')->middleware('CheckPermission:club_management,view,central');

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

        // Discipline Records Management Routes (Central)
        Route::get('/discipline-records', [DisciplineRecordController::class, 'index'])->name('discipline-records.index')->middleware('CheckPermission:discipline_record_management,view,central');
        Route::get('/discipline-records/summary', [DisciplineRecordController::class, 'summary'])->name('discipline-records.summary')->middleware('CheckPermission:discipline_record_management,view_summary,central');
        Route::get('/discipline-records/summary/export', [DisciplineRecordController::class, 'exportSummary'])->name('discipline-records.summary.export')->middleware('CheckPermission:discipline_record_management,view_summary,central');

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
        Route::get('/members/export/excel', [MemberController::class, 'exportMembersExcel'])->name('members.export')->middleware('CheckPermission:member_management,view,central');

        // Admin Settings routes
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('CheckPermission:setting_management,view,central');
        Route::post('/settings/general', [SettingController::class, 'updateGeneralSettings'])->name('settings.general.update')->middleware('CheckPermission:setting_management,general,central');
        Route::get('/settings/get-communes', [SettingController::class, 'getCommunes'])->name('settings.get-communes')->middleware('CheckPermission:setting_management,general,central');
        Route::post('/settings/mail', [SettingController::class, 'updateMailSettings'])->name('settings.mail.update')->middleware('CheckPermission:setting_management,mail,central');
        Route::post('/settings/mail-test', [SettingController::class, 'testMail'])->name('settings.mail.test')->middleware('CheckPermission:setting_management,mail,central');
        Route::post('/settings/refresh-permissions', [SettingController::class, 'refreshPermissions'])->name('settings.refresh-permissions')->middleware('CheckPermission:setting_management,general,central');
        Route::post('/settings/maintenance', [SettingController::class, 'updateMaintenanceSettings'])->name('settings.maintenance.update')->middleware('CheckPermission:setting_management,maintenance,central');
        Route::post('/settings/theme', [SettingController::class, 'updateThemeSettings'])->name('settings.theme.update')->middleware('CheckPermission:setting_management,general,central');
        Route::post('/settings/oauth', [SettingController::class, 'updateOAuthSettings'])->name('settings.update-oauth-settings')->middleware('CheckPermission:setting_management,oauth,central');

        // Notifications routes
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware('CheckPermission:notification_management,view,central');
        Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store')->middleware('CheckPermission:notification_management,create,central');
        Route::put('/notifications/{notification}', [NotificationController::class, 'update'])->name('notifications.update')->middleware('CheckPermission:notification_management,edit,central');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy')->middleware('CheckPermission:notification_management,delete,central');

        // Feature Requests routes
        Route::get('/feature-requests', [FeatureRequestController::class, 'index'])->name('feature-requests.index')->middleware('CheckPermission:feature_request_management,view,central');
        Route::put('/feature-requests/{featureRequest}', [FeatureRequestController::class, 'update'])->name('feature-requests.update')->middleware('CheckPermission:feature_request_management,edit,central');
        Route::delete('/feature-requests/{featureRequest}', [FeatureRequestController::class, 'destroy'])->name('feature-requests.destroy')->middleware('CheckPermission:feature_request_management,delete,central');

        // Central Lucky Draw routes
        Route::get('/lucky-draws', [LuckyDrawController::class, 'index'])->name('lucky-draws.index')->middleware('CheckPermission:lucky_draw_management,view,central');
        Route::post('/lucky-draws', [LuckyDrawController::class, 'store'])->name('lucky-draws.store')->middleware('CheckPermission:lucky_draw_management,create,central');
        Route::get('/lucky-draws/{luckyDraw}', [LuckyDrawController::class, 'show'])->name('lucky-draws.show')->middleware('CheckPermission:lucky_draw_management,view,central');
        Route::put('/lucky-draws/{luckyDraw}', [LuckyDrawController::class, 'update'])->name('lucky-draws.update')->middleware('CheckPermission:lucky_draw_management,edit,central');
        Route::delete('/lucky-draws/{luckyDraw}', [LuckyDrawController::class, 'destroy'])->name('lucky-draws.destroy')->middleware('CheckPermission:lucky_draw_management,delete,central');
        Route::post('/lucky-draws/{luckyDraw}/toggle-active', [LuckyDrawController::class, 'toggleActive'])->name('lucky-draws.toggle-active')->middleware('CheckPermission:lucky_draw_management,edit,central');

        // Central Lucky Draw Prizes routes
        Route::get('/lucky-draws/{luckyDraw}/prizes', [PrizeController::class, 'index'])->name('lucky-draws.prizes.index')->middleware('CheckPermission:lucky_draw_management,edit,central');
        Route::post('/lucky-draws/{luckyDraw}/prizes', [PrizeController::class, 'store'])->name('lucky-draws.prizes.store')->middleware('CheckPermission:lucky_draw_management,edit,central');
        Route::put('/lucky-draws/{luckyDraw}/prizes/{prize}', [PrizeController::class, 'update'])->name('lucky-draws.prizes.update')->middleware('CheckPermission:lucky_draw_management,edit,central');
        Route::delete('/lucky-draws/{luckyDraw}/prizes/{prize}', [PrizeController::class, 'destroy'])->name('lucky-draws.prizes.destroy')->middleware('CheckPermission:lucky_draw_management,edit,central');
        Route::post('/lucky-draws/{luckyDraw}/prizes/reorder', [PrizeController::class, 'reorder'])->name('lucky-draws.prizes.reorder')->middleware('CheckPermission:lucky_draw_management,edit,central');

        // Sessions routes (Central Admin only)
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index')->middleware('CheckPermission:session_management,view,central');
        Route::delete('/sessions/{sessionId}', [SessionController::class, 'destroy'])->name('sessions.destroy')->middleware('CheckPermission:session_management,delete,central');
        Route::delete('/sessions', [SessionController::class, 'destroyAll'])->name('sessions.destroy-all')->middleware('CheckPermission:session_management,delete,central');
        Route::delete('/sessions/expired', [SessionController::class, 'destroyExpired'])->name('sessions.destroy-expired')->middleware('CheckPermission:session_management,delete,central');
        Route::delete('/sessions/user/{userId}', [SessionController::class, 'destroyUserSessions'])->name('sessions.destroy-user')->middleware('CheckPermission:session_management,delete,central');

        // System Logs routes (Central Admin only)
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index')->middleware('CheckPermission:log_management,view,central');
        Route::delete('/logs/{log}', [LogController::class, 'destroy'])->name('logs.destroy')->middleware('CheckPermission:log_management,delete,central');
        Route::delete('/logs', [LogController::class, 'destroyAll'])->name('logs.destroy-all')->middleware('CheckPermission:log_management,delete,central');
        Route::delete('/logs/destroy/old', [LogController::class, 'destroyOld'])->name('logs.destroy-old')->middleware('CheckPermission:log_management,delete,central');
        Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export')->middleware('CheckPermission:log_management,export,central');
    });
});


Route::middleware(['auth', 'logsystem'])->prefix('club')->group(function () {
    Route::name('backend.club.')->group(function () {
        Route::get('/dashboard', [ClubDashboardController::class, 'index'])->name('dashboard')->middleware('CheckPermission:dashboard,view,branch');

        Route::get('/my-profile', [ClubMyProfileController::class, 'index'])->name('my-profile.index')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update', [ClubMyProfileController::class, 'update'])->name('my-profile.update')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-password', [ClubMyProfileController::class, 'updatePassword'])->name('my-profile.update-password')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-bank-account', [ClubMyProfileController::class, 'updateBankAccount'])->name('my-profile.update-bank-account')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::post('/my-profile/update-address', [ClubMyProfileController::class, 'updateAddress'])->name('my-profile.update-address')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::get('/my-profile/get-communes', [ClubMyProfileController::class, 'getCommunes'])->name('my-profile.get-communes')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::delete('/my-profile/destroy-session/{sessionId}', [ClubMyProfileController::class, 'destroySession'])->name('my-profile.destroy-session')->middleware('CheckPermission:member_management,my_profile,branch');
        Route::delete('/my-profile/destroy-all-sessions', [ClubMyProfileController::class, 'destroyAllSessions'])->name('my-profile.destroy-all-sessions')->middleware('CheckPermission:member_management,my_profile,branch');


        // Sessions routes
        Route::get('/sessions', [ClubSessionController::class, 'index'])->name('sessions.index')->middleware('CheckPermission:session_management,view,branch');
        Route::delete('/sessions/{sessionId}', [ClubSessionController::class, 'destroy'])->name('sessions.destroy')->middleware('CheckPermission:session_management,delete,branch');
        Route::delete('/sessions', [ClubSessionController::class, 'destroyAll'])->name('sessions.destroy-all')->middleware('CheckPermission:session_management,delete,branch');
        Route::delete('/sessions/expired', [ClubSessionController::class, 'destroyExpired'])->name('sessions.destroy-expired')->middleware('CheckPermission:session_management,delete,branch');
        Route::delete('/sessions/user/{userId}', [ClubSessionController::class, 'destroyUserSessions'])->name('sessions.destroy-user')->middleware('CheckPermission:session_management,delete,branch');

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
        Route::get('/members/check/email', [ClubMemberController::class, 'checkUserEmail'])->name('members.check-email')->middleware('CheckPermission:member_management,view,branch');
        Route::post('/members/assign/to-club', [ClubMemberController::class, 'assignToClub'])->name('members.assign-to-club')->middleware('CheckPermission:member_management,create,branch');
        Route::post('/members/import/excel', [ClubMemberController::class, 'import'])->name('members.import')->middleware('CheckPermission:member_management,create,branch');
        Route::get('/members/export/template', [ClubMemberController::class, 'exportTemplate'])->name('members.export-template')->middleware('CheckPermission:member_management,view,branch');
        Route::get('/members/export/excel', [ClubMemberController::class, 'exportMembersExcel'])->name('members.export')->middleware('CheckPermission:member_management,view,branch');

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

        // Disciplines routes
        Route::get('/disciplines', [ClubDisciplineController::class, 'index'])->name('disciplines.index')->middleware('CheckPermission:discipline_management,view,branch');
        Route::post('/disciplines', [ClubDisciplineController::class, 'store'])->name('disciplines.store')->middleware('CheckPermission:discipline_management,create,branch');
        Route::put('/disciplines/{discipline}', [ClubDisciplineController::class, 'update'])->name('disciplines.update')->middleware('CheckPermission:discipline_management,edit,branch');
        Route::delete('/disciplines/{discipline}', [ClubDisciplineController::class, 'destroy'])->name('disciplines.destroy')->middleware('CheckPermission:discipline_management,delete,branch');
        Route::post('/disciplines/import', [ClubDisciplineController::class, 'import'])->name('disciplines.import')->middleware('CheckPermission:discipline_management,create,branch');
        Route::get('/disciplines/export-template', [ClubDisciplineController::class, 'exportTemplate'])->name('disciplines.export-template')->middleware('CheckPermission:discipline_management,view,branch');

        // Discipline Records routes
        Route::get('/discipline-records', [ClubDisciplineRecordController::class, 'index'])->name('discipline-records.index')->middleware('CheckPermission:discipline_record_management,view,branch');
        Route::post('/discipline-records', [ClubDisciplineRecordController::class, 'store'])->name('discipline-records.store')->middleware('CheckPermission:discipline_record_management,create,branch');
        Route::put('/discipline-records/{disciplineRecord}', [ClubDisciplineRecordController::class, 'update'])->name('discipline-records.update')->middleware('CheckPermission:discipline_record_management,edit,branch');
        Route::patch('/discipline-records/{disciplineRecord}/revoke', [ClubDisciplineRecordController::class, 'revoke'])->name('discipline-records.revoke')->middleware('CheckPermission:discipline_record_management,revoke,branch');
        Route::delete('/discipline-records/{disciplineRecord}', [ClubDisciplineRecordController::class, 'destroy'])->name('discipline-records.destroy')->middleware('CheckPermission:discipline_record_management,delete,branch');
        Route::get('/discipline-records/summary', [ClubDisciplineRecordController::class, 'summary'])->name('discipline-records.summary')->middleware('CheckPermission:discipline_record_management,view_summary,branch');
        Route::get('/discipline-records/summary/export', [ClubDisciplineRecordController::class, 'exportSummary'])->name('discipline-records.summary.export')->middleware('CheckPermission:discipline_record_management,view_summary,branch');
        Route::get('/discipline-records/mypoints', [ClubMyPointsController::class, 'index'])->name('discipline-records.my-points')->middleware('CheckPermission:discipline_record_management,my_points,branch');
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
        // Get communes for address selection (AJAX)
        Route::get('/settings/get-communes', [ClubSettingController::class, 'getCommunes'])->name('settings.get-communes')->middleware('CheckPermission:setting_management,general,branch');
        // Update word commitment template
        Route::post('/settings/word-commitment-template', [ClubSettingController::class, 'updateWordCommitmentTemplate'])->name('settings.word-commitment-template.update')->middleware('CheckPermission:setting_management,word_template,branch');
        // Download word commitment template
        Route::get('/settings/word-commitment-template/download', [ClubSettingController::class, 'downloadWordCommitmentTemplate'])->name('settings.word-commitment-template.download')->middleware('CheckPermission:setting_management,word_template,branch');
        // Bank settings routes
        Route::get('/settings/banks', [ClubSettingController::class, 'getBanksFromAPI'])->name('settings.banks.api')->middleware('CheckPermission:setting_management,payment,branch');
        Route::post('/settings/bank', [ClubSettingController::class, 'updateBankSettings'])->name('settings.bank.update')->middleware('CheckPermission:setting_management,payment,branch');
        // Mail settings route (SMTP/Outlook)
        Route::post('/settings/mail', [ClubSettingController::class, 'updateMailSettings'])->name('settings.mail.update')->middleware('CheckPermission:setting_management,mail,branch');
        // Test gửi mail
        Route::post('/settings/mail-test', [ClubSettingController::class, 'testMail'])->name('settings.mail.test')->middleware('CheckPermission:setting_management,mail,branch');

        // Notifications routes - API endpoints first to avoid conflict with {notification} route
        Route::get('/notifications', [ClubNotificationController::class, 'index'])->name('notifications.index')->middleware('CheckPermission:notification_management,view,branch');
        Route::get('/notifications/unread-count/get', [ClubNotificationController::class, 'unreadCount'])->name('notifications.unread-count')->middleware('CheckPermission:notification_management,view,branch');
        Route::get('/notifications/recent', [ClubNotificationController::class, 'recent'])->name('notifications.recent')->middleware('CheckPermission:notification_management,view,branch');
        Route::get('/notifications/latest', [ClubNotificationController::class, 'latest'])->name('notifications.latest')->middleware('CheckPermission:notification_management,view,branch');
        Route::post('/notifications/mark-all-as-read', [ClubNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read')->middleware('CheckPermission:notification_management,view,branch');
        Route::get('/notifications/{notification}', [ClubNotificationController::class, 'show'])->name('notifications.show')->middleware('CheckPermission:notification_management,view,branch');
        Route::post('/notifications/{notification}/mark-as-read', [ClubNotificationController::class, 'markAsRead'])->name('notifications.mark-as-read')->middleware('CheckPermission:notification_management,view,branch');

        // Feature Requests routes
        Route::get('/feature-requests', [ClubFeatureRequestController::class, 'index'])->name('feature-requests.index')->middleware('CheckPermission:feature_request_management,view,branch');
        Route::post('/feature-requests', [ClubFeatureRequestController::class, 'store'])->name('feature-requests.store')->middleware('CheckPermission:feature_request_management,create,branch');

        // Lucky Draw routes
        Route::get('/lucky-draws', [ClubLuckyDrawController::class, 'index'])->name('lucky-draws.index')->middleware('CheckPermission:lucky_draw_management,view,branch');
        Route::get('/lucky-draws/create', [ClubLuckyDrawController::class, 'create'])->name('lucky-draws.create')->middleware('CheckPermission:lucky_draw_management,create,branch');
        Route::post('/lucky-draws', [ClubLuckyDrawController::class, 'store'])->name('lucky-draws.store')->middleware('CheckPermission:lucky_draw_management,create,branch');
        Route::get('/lucky-draws/{luckyDraw}', [ClubLuckyDrawController::class, 'show'])->name('lucky-draws.show')->middleware('CheckPermission:lucky_draw_management,view,branch');
        Route::get('/lucky-draws/{luckyDraw}/edit', [ClubLuckyDrawController::class, 'edit'])->name('lucky-draws.edit')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::put('/lucky-draws/{luckyDraw}', [ClubLuckyDrawController::class, 'update'])->name('lucky-draws.update')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::delete('/lucky-draws/{luckyDraw}', [ClubLuckyDrawController::class, 'destroy'])->name('lucky-draws.destroy')->middleware('CheckPermission:lucky_draw_management,delete,branch');
        Route::post('/lucky-draws/{luckyDraw}/toggle-active', [ClubLuckyDrawController::class, 'toggleActive'])->name('lucky-draws.toggle-active')->middleware('CheckPermission:lucky_draw_management,edit,branch');

        // Lucky Draw Prizes routes
        Route::get('/lucky-draws/{luckyDraw}/prizes', [ClubPrizeController::class, 'index'])->name('lucky-draws.prizes.index')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::post('/lucky-draws/{luckyDraw}/prizes', [ClubPrizeController::class, 'store'])->name('lucky-draws.prizes.store')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::put('/lucky-draws/{luckyDraw}/prizes/{prize}', [ClubPrizeController::class, 'update'])->name('lucky-draws.prizes.update')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::delete('/lucky-draws/{luckyDraw}/prizes/{prize}', [ClubPrizeController::class, 'destroy'])->name('lucky-draws.prizes.destroy')->middleware('CheckPermission:lucky_draw_management,edit,branch');
        Route::post('/lucky-draws/{luckyDraw}/prizes/reorder', [ClubPrizeController::class, 'reorder'])->name('lucky-draws.prizes.reorder')->middleware('CheckPermission:lucky_draw_management,edit,branch');
    });
});

Route::middleware(['auth', 'logsystem'])->group(function () {
    Route::post('/switch-default-club', [SwitchClubController::class, 'switch'])->name('switch.default.club')->middleware('auth');
});

// Public Lucky Draw routes (no authentication required)
Route::prefix('lucky-draw')->name('lucky-draw.')->middleware('logsystem')->group(function () {
    Route::get('/{slug}', [LuckyDrawPublicController::class, 'show'])->name('show');
    Route::get('/{slug}/prizes', [LuckyDrawPublicController::class, 'getPrizes'])->name('prizes');
    Route::get('/{slug}/prizes-details', [LuckyDrawPublicController::class, 'getPrizesWithDetails'])->name('prizes.details');
    Route::get('/{slug}/participants', [LuckyDrawPublicController::class, 'getParticipants'])->name('participants');
    Route::get('/{slug}/winners', [LuckyDrawPublicController::class, 'getWinners'])->name('winners');
    Route::post('/{slug}/check-eligibility', [LuckyDrawPublicController::class, 'checkParticipantEligibility'])->name('check-eligibility');
    Route::post('/{slug}/api', [LuckyDrawPublicController::class, 'handleApi'])->name('api');
});
