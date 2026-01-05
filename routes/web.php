<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;

// Admin Controllers
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\ClubMemberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AssignRoleController;


/*
|-------------------------------------------------------------------------- 
| Guest Routes
|-------------------------------------------------------------------------- 
*/
Route::get('/', [GuestController::class, 'index'])->name('guest.home');
Route::get('/clubs', [GuestController::class, 'clubs'])->name('guest.clubs');
Route::get('/club/{id}', [GuestController::class, 'clubDetail'])->name('guest.club-detail');
Route::get('/events', [GuestController::class, 'events'])->name('guest.events');
Route::get('/about', [GuestController::class, 'about'])->name('guest.about');
Route::get('/contact', [GuestController::class, 'contact'])->name('guest.contact');
Route::post('/contact', [GuestController::class, 'submitContact'])->name('guest.contact.submit');
Route::get('/faq', [GuestController::class, 'faq'])->name('guest.faq');
Route::get('/privacy', [GuestController::class, 'privacy'])->name('guest.privacy');

/*
|-------------------------------------------------------------------------- 
| AUTH Routes
|-------------------------------------------------------------------------- 
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/verify-otp', [AuthController::class, 'showVerifyOtpForm'])->name('verify.otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp.post');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');

/*
|-------------------------------------------------------------------------- 
| Student Routes
|-------------------------------------------------------------------------- 
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/student/home', [StudentController::class, 'home'])->name('student.home');
    
    // Routes cho "CLB của tôi"
    Route::get('/student/my-clubs', [StudentController::class, 'myClubs'])->name('student.my-clubs');
    Route::get('/student/club/{id}', [StudentController::class, 'clubDetail'])->name('student.club-detail');
    Route::post('/student/event/{id}/register', [StudentController::class, 'registerEvent'])->name('student.register-event');
    Route::post('/student/event-registration/{id}/cancel', [StudentController::class, 'cancelEventRegistration'])->name('student.cancel-event-registration');
    Route::post('/student/club/{id}/leave', [StudentController::class, 'leaveClub'])->name('student.leave-club');
    
    // Routes cho Hồ Sơ Cá Nhân
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    
    // Upload Avatar
    Route::post('/student/upload-avatar', [StudentController::class, 'uploadAvatar'])->name('student.upload-avatar');
    
    // Routes cho Cài đặt tài khoản
    Route::get('/student/settings', [StudentController::class, 'settings'])->name('student.settings');
    Route::post('/student/settings/update', [StudentController::class, 'updateSettings'])->name('student.settings.update');
    
    // Routes cho Đổi mật khẩu
    Route::get('/student/change-password', [StudentController::class, 'showChangePasswordForm'])->name('student.change-password');
    Route::post('/student/change-password', [StudentController::class, 'changePassword'])->name('student.change-password.post');
    
    // Routes cho Thông báo
    // THÔNG BÁO HỆ THỐNG (SINH VIÊN)
    Route::get('/student/notifications', [\App\Http\Controllers\Student\NotificationController::class, 'inbox'])->name('student.notifications');
    Route::get('/student/notifications/{id}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])->name('student.notifications.show');
    
    // Route cho đăng xuất tất cả thiết bị
    Route::post('/student/logout-all', [AuthController::class, 'logoutAll'])->name('student.logout-all');
    
    // Routes cho Danh Sách CLB
    Route::get('/student/all-clubs', [StudentController::class, 'allClubs'])->name('student.all-clubs');
    Route::get('/student/club-public/{id}', [StudentController::class, 'clubPublicDetail'])->name('student.club-public-detail');
    Route::post('/student/club/{id}/register', [StudentController::class, 'registerClub'])->name('student.register-club');
    
    // Routes cho Hoạt Động CLB
    Route::get('/student/activities', [StudentController::class, 'activities'])->name('student.activities');
    Route::get('/student/activity/{id}', [StudentController::class, 'activityDetail'])->name('student.activity-detail');
    
    // Routes cho Đề xuất hoạt động (Sinh viên)
    Route::get('/student/propose-event', [StudentController::class, 'proposeEvent'])->name('student.propose-event');
    Route::post('/student/propose-event', [StudentController::class, 'storeProposedEvent'])->name('student.store-proposed-event');
    Route::get('/student/proposal/{id}', [StudentController::class, 'proposalDetail'])->name('student.proposal-detail');
    
    // HỖ TRỢ (SINH VIÊN)
    Route::prefix('student/support')->name('student.support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SupportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Student\SupportController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Student\SupportController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Student\SupportController::class, 'show'])->name('show');
        Route::post('/{id}/respond', [\App\Http\Controllers\Student\SupportController::class, 'respond'])->name('respond');
    });
    
    // THỐNG KÊ - CÁ NHÂN (SINH VIÊN)
    Route::get('/student/personal-statistics', [StudentController::class, 'personalStatistics'])->name('student.personal-statistics');
    Route::prefix('student/personal-statistics')->name('student.personal-statistics.')->group(function () {
        Route::get('/activities', [StudentController::class, 'personalStatisticsActivities'])->name('activities');
        Route::get('/points', [StudentController::class, 'personalStatisticsPoints'])->name('points');
        Route::get('/club-history', [StudentController::class, 'personalStatisticsClubHistory'])->name('club-history');
        Route::get('/violations', [StudentController::class, 'personalStatisticsViolations'])->name('violations');
    });
    
    // Routes cho Chủ nhiệm CLB
    Route::prefix('student/chairman')
        ->name('student.chairman.')
        ->group(function () {
            // Dashboard Chủ nhiệm
            Route::get('/dashboard', [\App\Http\Controllers\Student\ChairmanController::class, 'dashboard'])->name('dashboard');
            
            // Quản lý thành viên
            Route::get('/manage-members', [\App\Http\Controllers\Student\ChairmanController::class, 'manageMembers'])->name('manage-members');
            Route::post('/add-member', [\App\Http\Controllers\Student\ChairmanController::class, 'addMember'])->name('add-member');
            Route::put('/update-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'updateMember'])->name('update-member');
            Route::delete('/remove-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'removeMember'])->name('remove-member');
            Route::post('/approve-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'approveMember'])->name('approve-member');
            Route::post('/reject-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'rejectMember'])->name('reject-member');
            Route::post('/suspend-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'suspendMember'])->name('suspend-member');
            Route::post('/activate-member/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'activateMember'])->name('activate-member');
            
            // TRANG 1: Quản lý đơn đăng ký vào CLB
            Route::get('/manage-registrations', [\App\Http\Controllers\Student\ChairmanController::class, 'manageRegistrations'])->name('manage-registrations');
            Route::post('/approve-registration/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'approveRegistration'])->name('approve-registration');
            Route::post('/reject-registration/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'rejectRegistration'])->name('reject-registration');
            
            // TRANG 2: Gán chức vụ
            Route::get('/manage-positions', [\App\Http\Controllers\Student\ChairmanController::class, 'managePositions'])->name('manage-positions');
            Route::put('/update-position/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'updatePosition'])->name('update-position');
            
            // HOẠT ĐỘNG CLB
            Route::get('/create-event', [\App\Http\Controllers\Student\ChairmanController::class, 'createEvent'])->name('create-event');
            Route::get('/pending-events', [\App\Http\Controllers\Student\ChairmanController::class, 'pendingEvents'])->name('pending-events');
            Route::get('/approved-events', [\App\Http\Controllers\Student\ChairmanController::class, 'approvedEvents'])->name('approved-events');
            Route::get('/pending-registrations', [\App\Http\Controllers\Student\ChairmanController::class, 'pendingRegistrations'])->name('pending-registrations');
            Route::get('/approved-participants', [\App\Http\Controllers\Student\ChairmanController::class, 'approvedParticipants'])->name('approved-participants');
            
            // CRUD Events
            Route::post('/store-event', [\App\Http\Controllers\Student\ChairmanController::class, 'storeEvent'])->name('store-event');
            Route::put('/update-event/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'updateEvent'])->name('update-event');
            Route::delete('/delete-event/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'deleteEvent'])->name('delete-event');
            
            // Duyệt đăng ký tham gia hoạt động
            Route::post('/approve-event-registration/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'approveEventParticipant'])->name('approve-event-registration');
            Route::post('/approve-bulk-registrations', [\App\Http\Controllers\Student\ChairmanController::class, 'approveBulkRegistrations'])->name('approve-bulk-registrations');
            Route::post('/reject-event-registration/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'rejectEventParticipant'])->name('reject-event-registration');
            
            // THỐNG KÊ & BÁO CÁO (CHỦ NHIỆM CLB)
            Route::get('/statistics', [\App\Http\Controllers\Student\ChairmanController::class, 'statistics'])->name('statistics'); // Tổng quan CLB
            Route::get('/statistics/members', [\App\Http\Controllers\Student\ChairmanController::class, 'memberStatistics'])->name('statistics.members'); // Thống kê thành viên
            Route::get('/statistics/activities', [\App\Http\Controllers\Student\ChairmanController::class, 'activityStatistics'])->name('statistics.activities'); // Thống kê hoạt động
            Route::get('/participation-statistics', [\App\Http\Controllers\Student\ChairmanController::class, 'participationStatistics'])->name('participation-statistics'); // Thống kê tham gia
            Route::get('/statistics/violations', [\App\Http\Controllers\Student\ChairmanController::class, 'violationStatistics'])->name('statistics.violations'); // Thống kê vi phạm
            Route::get('/export-report', [\App\Http\Controllers\Student\ChairmanController::class, 'exportReport'])->name('export-report'); // Xuất báo cáo
            Route::post('/export-report/generate', [\App\Http\Controllers\Student\ChairmanController::class, 'generateReport'])->name('export-report.generate');
            
            // Duyệt đề xuất hoạt động
            Route::get('/approve-proposals', [\App\Http\Controllers\Student\ChairmanController::class, 'approveProposals'])->name('approve-proposals');
            Route::post('/approve-proposal/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'approveProposal'])->name('approve-proposal');
            Route::post('/reject-proposal/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'rejectProposal'])->name('reject-proposal');
            
            // Danh sách đề xuất hoạt động
            Route::get('/event-proposals', [\App\Http\Controllers\Student\ChairmanController::class, 'eventProposals'])->name('event-proposals');
            
            // TRANG 5: Duyệt hoạt động
            Route::get('/approve-activities', [\App\Http\Controllers\Student\ChairmanController::class, 'approveActivities'])->name('approve-activities');
            Route::post('/approve-activity-points/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'approveActivityPoints'])->name('approve-activity-points');
            Route::get('/activity-points-history', [\App\Http\Controllers\Student\ChairmanController::class, 'activityPointsHistory'])->name('activity-points-history');
            
            // TRANG 6: Thông tin CLB
            Route::get('/club-info', [\App\Http\Controllers\Student\ChairmanController::class, 'clubInfo'])->name('club-info');
            Route::post('/club-info/update', [\App\Http\Controllers\Student\ChairmanController::class, 'updateClubInfo'])->name('club-info.update');
            
            // THÔNG BÁO HỆ THỐNG (CHỦ NHIỆM CLB)
            Route::get('/notifications/inbox', [\App\Http\Controllers\Student\Chairman\NotificationController::class, 'inbox'])->name('notifications.inbox');
            Route::get('/notifications/send', [\App\Http\Controllers\Student\Chairman\NotificationController::class, 'create'])->name('notifications.send');
            Route::post('/notifications', [\App\Http\Controllers\Student\Chairman\NotificationController::class, 'store'])->name('notifications.store');
            Route::get('/notifications/history', [\App\Http\Controllers\Student\Chairman\NotificationController::class, 'history'])->name('notifications.history');
            Route::get('/notifications/{id}', [\App\Http\Controllers\Student\Chairman\NotificationController::class, 'show'])->name('notifications.show');
            
            // HỖ TRỢ (CHỦ NHIỆM CLB)
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Student\Chairman\SupportController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Student\Chairman\SupportController::class, 'create'])->name('create');
                Route::post('/store', [\App\Http\Controllers\Student\Chairman\SupportController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Student\Chairman\SupportController::class, 'show'])->name('show');
                Route::post('/{id}/respond', [\App\Http\Controllers\Student\Chairman\SupportController::class, 'respond'])->name('respond');
            });
            
            // NỘI QUY - VI PHẠM
            Route::get('/regulations', [\App\Http\Controllers\Student\ChairmanController::class, 'regulations'])->name('regulations.index');
            Route::get('/violations', [\App\Http\Controllers\Student\ChairmanController::class, 'violations'])->name('violations.index');
            Route::get('/violations/create', [\App\Http\Controllers\Student\ChairmanController::class, 'createViolation'])->name('violations.create');
            Route::post('/violations', [\App\Http\Controllers\Student\ChairmanController::class, 'storeViolation'])->name('violations.store');
            Route::get('/violations/{id}', [\App\Http\Controllers\Student\ChairmanController::class, 'showViolation'])->name('violations.show');
            
            // LỊCH SỬ KỶ LUẬT
            Route::get('/discipline-history/by-member', [\App\Http\Controllers\Student\ChairmanController::class, 'disciplineHistoryByMember'])->name('discipline-history.by-member');
            Route::get('/discipline-history/by-time', [\App\Http\Controllers\Student\ChairmanController::class, 'disciplineHistoryByTime'])->name('discipline-history.by-time');
        });
});


/*
|-------------------------------------------------------------------------- 
| Admin Routes (Web – chuẩn Laravel, KHÔNG dùng API) 
|-------------------------------------------------------------------------- 
*/
Route::middleware(['web', 'auth', 'adminOnly'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Upload Avatar
        Route::post('/upload-avatar', [AdminController::class, 'uploadAvatar'])->name('upload-avatar');

        /*
        |-------------------------------------------------------------------------- 
        | CLUB ROUTES (FULL CRUD – CHUẨN MVC) 
        |-------------------------------------------------------------------------- 
        */
        Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
        // Return next club code (AJAX) used to show auto code in create modal
        Route::get('/clubs/next-code', [ClubController::class, 'nextCode'])->name('clubs.next-code');
        Route::post('/clubs/store', [ClubController::class, 'store'])->name('clubs.store');
        Route::put('/clubs/update/{id}', [ClubController::class, 'update'])->name('clubs.update');
        Route::delete('/clubs/delete/{id}', [ClubController::class, 'destroy'])->name('clubs.delete');
        
        /*
        |-------------------------------------------------------------------------- 
        | CLUB MEMBERS ROUTES
        |-------------------------------------------------------------------------- 
        */
        Route::get('/club-members', [ClubMemberController::class, 'index'])->name('club-members.index');
        Route::post('/club-members/store', [ClubMemberController::class, 'store'])->name('club-members.store');
        Route::put('/club-members/update/{id}', [ClubMemberController::class, 'update'])->name('club-members.update');
        Route::delete('/club-members/destroy/{id}', [ClubMemberController::class, 'destroy'])->name('club-members.destroy');
        Route::post('/club-members/approve/{id}', [ClubMemberController::class, 'approve'])->name('club-members.approve');
        Route::post('/club-members/reject/{id}', [ClubMemberController::class, 'reject'])->name('club-members.reject');
        Route::post('/club-members/suspend/{id}', [ClubMemberController::class, 'suspend'])->name('club-members.suspend');
        Route::post('/club-members/activate/{id}', [ClubMemberController::class, 'activate'])->name('club-members.activate');

        /*
        |-------------------------------------------------------------------------- 
        | USERS ROUTES 
        |-------------------------------------------------------------------------- 
        */
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        // Ajax search for users (Select2 autocomplete)
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/reset', [UserController::class, 'resetPassword'])->name('users.reset');
        Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::post('/users/check-mssv', [UserController::class, 'checkMSSV'])->name('users.check-mssv');

        /*
        |-------------------------------------------------------------------------- 
        | ROLES ROUTES 
        |-------------------------------------------------------------------------- 
        */
        Route::resource('roles', RoleController::class);

        /*
        |-------------------------------------------------------------------------- 
        | ASSIGN ROLE ROUTES 
        |-------------------------------------------------------------------------- 
        */
        Route::get('/assign', [AssignRoleController::class, 'index'])->name('assign.index');
        Route::put('/assign/{id}', [AssignRoleController::class, 'update'])->name('assign.update');

        /*
        |-------------------------------------------------------------------------- 
        | ACTIVITIES ROUTES (HOẠT ĐỘNG)
        |-------------------------------------------------------------------------- 
        */
        // Danh sách hoạt động
        Route::get('/activities', [\App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/{id}/detail', [\App\Http\Controllers\Admin\ActivityController::class, 'show'])->name('activities.show');
        
        // Hoạt động vi phạm
        Route::get('/activities/violations', [\App\Http\Controllers\Admin\ActivityController::class, 'violations'])->name('activities.violations');
        Route::get('/activities/{id}/disable', [\App\Http\Controllers\Admin\ActivityController::class, 'showDisableForm'])->name('activities.show-disable');
        Route::post('/activities/{id}/disable', [\App\Http\Controllers\Admin\ActivityController::class, 'disable'])->name('activities.disable');
        Route::get('/activities/{id}/update-violation', [\App\Http\Controllers\Admin\ActivityController::class, 'showUpdateViolationForm'])->name('activities.show-update-violation');
        Route::put('/activities/{id}/violation', [\App\Http\Controllers\Admin\ActivityController::class, 'updateViolation'])->name('activities.update-violation');
        Route::delete('/activities/{id}', [\App\Http\Controllers\Admin\ActivityController::class, 'destroy'])->name('activities.destroy');
        
        // Thống kê hoạt động (giữ lại để tương thích)
        Route::get('/activities/statistics/by-club', [\App\Http\Controllers\Admin\ActivityController::class, 'statisticsByClub'])->name('activities.statistics.by-club');
        Route::get('/activities/statistics/by-time', [\App\Http\Controllers\Admin\ActivityController::class, 'statisticsByTime'])->name('activities.statistics.by-time');
        Route::get('/activities/statistics/export', [\App\Http\Controllers\Admin\ActivityController::class, 'exportReport'])->name('activities.statistics.export');
        Route::post('/activities/statistics/export/generate', [\App\Http\Controllers\Admin\ActivityController::class, 'generateExportReport'])->name('activities.statistics.export.generate');

        /*
        |-------------------------------------------------------------------------- 
        | STATISTICS & REPORTS ROUTES (THỐNG KÊ - BÁO CÁO)
        |-------------------------------------------------------------------------- 
        */
        Route::get('/statistics/overview', [\App\Http\Controllers\Admin\StatisticsController::class, 'overview'])->name('statistics.overview');
        Route::get('/statistics/clubs', [\App\Http\Controllers\Admin\StatisticsController::class, 'clubs'])->name('statistics.clubs');
        Route::get('/statistics/members', [\App\Http\Controllers\Admin\StatisticsController::class, 'members'])->name('statistics.members');
        Route::get('/statistics/activities', [\App\Http\Controllers\Admin\StatisticsController::class, 'activities'])->name('statistics.activities');
        Route::get('/statistics/violations', [\App\Http\Controllers\Admin\StatisticsController::class, 'violations'])->name('statistics.violations');
        Route::get('/statistics/financial', [\App\Http\Controllers\Admin\StatisticsController::class, 'financial'])->name('statistics.financial');
        Route::get('/statistics/export', [\App\Http\Controllers\Admin\StatisticsController::class, 'export'])->name('statistics.export');
        Route::post('/statistics/export/generate', [\App\Http\Controllers\Admin\StatisticsController::class, 'generateExport'])->name('statistics.export.generate');

        /*
        |-------------------------------------------------------------------------- 
        | REGULATIONS & VIOLATIONS ROUTES
        |-------------------------------------------------------------------------- 
        */
        // Nội quy
        Route::get('/regulations', [\App\Http\Controllers\Admin\RegulationController::class, 'index'])->name('regulations.index');
        Route::get('/regulations/create', [\App\Http\Controllers\Admin\RegulationController::class, 'create'])->name('regulations.create');
        Route::post('/regulations', [\App\Http\Controllers\Admin\RegulationController::class, 'store'])->name('regulations.store');
        Route::get('/regulations/{id}/edit', [\App\Http\Controllers\Admin\RegulationController::class, 'edit'])->name('regulations.edit');
        Route::put('/regulations/{id}', [\App\Http\Controllers\Admin\RegulationController::class, 'update'])->name('regulations.update');
        Route::get('/regulations/{id}', [\App\Http\Controllers\Admin\RegulationController::class, 'show'])->name('regulations.show');
        Route::post('/regulations/{id}/toggle-status', [\App\Http\Controllers\Admin\RegulationController::class, 'toggleStatus'])->name('regulations.toggle-status');

        // Vi phạm & Kỷ luật
        Route::get('/violations', [\App\Http\Controllers\Admin\ViolationController::class, 'index'])->name('violations.index');
        Route::get('/violations/export', [\App\Http\Controllers\Admin\ViolationController::class, 'export'])->name('violations.export');
        Route::get('/violations/handle', [\App\Http\Controllers\Admin\ViolationController::class, 'handleList'])->name('violations.handle-list');
        Route::get('/violations/history', [\App\Http\Controllers\Admin\ViolationController::class, 'history'])->name('violations.history');
        Route::get('/violations/history/export', [\App\Http\Controllers\Admin\ViolationController::class, 'exportHistory'])->name('violations.export-history');
        Route::get('/violations/{id}/handle', [\App\Http\Controllers\Admin\ViolationController::class, 'handle'])->name('violations.handle');
        Route::post('/violations/{id}/handle', [\App\Http\Controllers\Admin\ViolationController::class, 'processDiscipline'])->name('violations.process-discipline');
        Route::get('/violations/{id}', [\App\Http\Controllers\Admin\ViolationController::class, 'show'])->name('violations.show');

        /*
        |-------------------------------------------------------------------------- 
        | NOTIFICATIONS ROUTES (THÔNG BÁO HỆ THỐNG)
        |-------------------------------------------------------------------------- 
        */
        Route::get('/notifications/inbox', [\App\Http\Controllers\Admin\NotificationController::class, 'inbox'])->name('notifications.inbox');
        Route::get('/notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.send');
        Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
        Route::get('/notifications/history', [\App\Http\Controllers\Admin\NotificationController::class, 'history'])->name('notifications.history');
        Route::get('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');

        // VẬN HÀNH HỆ THỐNG - Sao lưu dữ liệu
        Route::get('/backup', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/create', [\App\Http\Controllers\Admin\BackupController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/auto-config', [\App\Http\Controllers\Admin\BackupController::class, 'updateAutoBackup'])->name('backup.auto-config');
        Route::get('/backup/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'delete'])->name('backup.delete');
        Route::post('/backup/restore/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backup.restore');

        // VẬN HÀNH HỆ THỐNG - Nhật ký Admin
        Route::get('/admin-log', [\App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('admin-log.index');
        Route::get('/admin-log/{id}', [\App\Http\Controllers\Admin\AdminLogController::class, 'show'])->name('admin-log.show');
        Route::get('/admin-log/export', [\App\Http\Controllers\Admin\AdminLogController::class, 'export'])->name('admin-log.export');

        // QUẢN LÝ HỆ THỐNG - Hỗ trợ / Liên hệ
        Route::prefix('support')->name('support.')->group(function () {
            // Liên hệ từ Guest
            Route::get('/guest-contacts', [\App\Http\Controllers\Admin\SupportController::class, 'guestContacts'])->name('guest-contacts');
            
            // Yêu cầu từ Sinh viên
            Route::get('/student-requests', [\App\Http\Controllers\Admin\SupportController::class, 'studentRequests'])->name('student-requests');
            
            // Yêu cầu từ Chủ nhiệm CLB
            Route::get('/chairman-requests', [\App\Http\Controllers\Admin\SupportController::class, 'chairmanRequests'])->name('chairman-requests');
            
            // Chi tiết và xử lý
            Route::get('/{id}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
            Route::post('/{id}/respond', [\App\Http\Controllers\Admin\SupportController::class, 'respond'])->name('respond');
            Route::post('/{id}/update-status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/mark-processed', [\App\Http\Controllers\Admin\SupportController::class, 'markAsProcessed'])->name('mark-processed');
        });

        // VẬN HÀNH HỆ THỐNG - Cấu hình hệ thống
        Route::prefix('system-config')->name('system-config.')->group(function () {
            Route::get('/website', [\App\Http\Controllers\Admin\SystemConfigController::class, 'website'])->name('website');
            Route::post('/website', [\App\Http\Controllers\Admin\SystemConfigController::class, 'updateWebsite'])->name('website.update');
            Route::get('/email', [\App\Http\Controllers\Admin\SystemConfigController::class, 'email'])->name('email');
            Route::post('/email', [\App\Http\Controllers\Admin\SystemConfigController::class, 'updateEmail'])->name('email.update');
            Route::post('/email/test', [\App\Http\Controllers\Admin\SystemConfigController::class, 'testEmail'])->name('email.test');
            Route::get('/logo', [\App\Http\Controllers\Admin\LogoBannerController::class, 'index'])->name('logo');
            Route::post('/logo/upload-logo', [\App\Http\Controllers\Admin\LogoBannerController::class, 'uploadLogo'])->name('logo.upload-logo');
            Route::post('/logo/upload-favicon', [\App\Http\Controllers\Admin\LogoBannerController::class, 'uploadFavicon'])->name('logo.upload-favicon');
            Route::post('/logo/upload-banner-home', [\App\Http\Controllers\Admin\LogoBannerController::class, 'uploadBannerHome'])->name('logo.upload-banner-home');
            Route::post('/logo/upload-banner-login', [\App\Http\Controllers\Admin\LogoBannerController::class, 'uploadBannerLogin'])->name('logo.upload-banner-login');
            Route::delete('/logo/{type}', [\App\Http\Controllers\Admin\LogoBannerController::class, 'delete'])->name('logo.delete');
            Route::get('/points', [\App\Http\Controllers\Admin\PointsConfigController::class, 'index'])->name('points');
            Route::post('/points', [\App\Http\Controllers\Admin\PointsConfigController::class, 'update'])->name('points.update');
        });

    });
