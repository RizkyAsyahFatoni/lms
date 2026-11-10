<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\IdpController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ExternalTrainingController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\OpdRequestController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\InClassController;
use App\Http\Controllers\Admin\SimpegController;
use App\Http\Controllers\Admin\VerificationCenterController;

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/webinars', [WebinarController::class, 'index'])->name('webinars.index');
Route::get('/webinars/{slug}', [WebinarController::class, 'show'])->name('webinars.show');
Route::get('/in-class', [InClassController::class, 'index'])->name('in-class.index');
Route::get('/certificates/{number}', [CertificateController::class, 'show'])->name('certificates.show');
Route::get('/certificates/verify/{number}', [CertificateController::class, 'show'])->name('certificates.verify');
Route::get('/certificate/{number}', [CertificateController::class, 'show']);
Route::get('/certificate/verify/{number}', [CertificateController::class, 'show']);

// Kalender Pembelajaran Publik (KAK XVII) — tampil untuk umum, filter visibility per login status
Route::get('/kalender', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/kalender/events', [CalendarController::class, 'events'])->name('calendar.events');

// Artikel, Regulasi & Repositori Pengetahuan (KAK XVI Knowledge Management)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/attachments/{attachment}/download', [ArticleController::class, 'downloadAttachment'])->name('articles.attachments.download');

// Rute Autentikasi Single Sign-On (SSO)
Route::get('/auth/sso/redirect', [SsoController::class, 'redirect'])->name('auth.sso.redirect');
Route::get('/auth/sso/callback', [SsoController::class, 'callback'])->name('auth.sso.callback');

// Google OAuth 2.0 Authentication Routes
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Simulasi Mock Login hanya boleh aktif pada lingkungan pengembangan lokal & testing
if (app()->environment('local', 'testing')) {
    Route::post('/auth/sso/mock-login', [SsoController::class, 'mockLogin'])->name('auth.sso.mock');
    Route::post('/auth/google/mock-login', [GoogleAuthController::class, 'mockLogin'])->name('auth.google.mock');
}

// Trainer / Instruktur Registration
Route::get('/trainer/register', [\App\Http\Controllers\TrainerRegistrationController::class, 'show'])->name('trainer.register');
Route::post('/trainer/register', [\App\Http\Controllers\TrainerRegistrationController::class, 'submit'])->name('trainer.register.post')->middleware('auth');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(['throttle:5,1', 'brute.force']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1');
});


Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Student Portal & Protected Learning Routes
Route::middleware('auth')->group(function () {
    // Multi-Role Specific Dashboards (KAK VII, VIII, IX & Corpu Berau)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/eksekutif', [DashboardController::class, 'pimpinanDashboard'])->name('dashboard.pimpinan')->middleware('role:pimpinan,super_admin,admin,bkpsdm');
    Route::get('/dashboard/opd', [DashboardController::class, 'adminOpdDashboard'])->name('dashboard.admin_opd')->middleware('role:admin_opd,super_admin,admin,bkpsdm');
    Route::get('/dashboard/narasumber', [DashboardController::class, 'narasumberDashboard'])->name('dashboard.narasumber')->middleware('role:narasumber,instructor,super_admin,admin');
    Route::get('/dashboard/talenta', [DashboardController::class, 'reviewerTalentDashboard'])->name('dashboard.reviewer_talent')->middleware('role:reviewer_talent,super_admin,admin,bkpsdm');
    Route::get('/dashboard/belajar', [DashboardController::class, 'learnerDashboard'])->name('dashboard.learner');
    Route::post('/narasumber/submissions/{submission}/grade', [AdminController::class, 'gradeSubmission'])->name('narasumber.submissions.grade')->middleware('role:narasumber,instructor,super_admin,admin');
    Route::post('/courses/{slug}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{slug}/attendance', [CourseController::class, 'recordAttendance'])->name('courses.attendance');
    
    // Learning Player
    Route::get('/learn/{course:slug}/{lesson:slug?}', [ClassroomController::class, 'show'])->name('classroom.show');
    Route::post('/learn/{course:slug}/{lesson:slug}/complete', [ClassroomController::class, 'toggleComplete'])->name('classroom.complete');
    Route::post('/learn/{course:slug}/{lesson:slug}/quiz-submit', [ClassroomController::class, 'submitQuiz'])->name('classroom.quiz.submit');
    Route::post('/learn/{course:slug}/{lesson:slug}/assignment-submit', [ClassroomController::class, 'submitAssignment'])->name('classroom.assignment.submit');

    // External JP Submissions (Pelatihan Luar)
    Route::get('/jp-eksternal', [ExternalTrainingController::class, 'index'])->name('external-trainings.index');
    Route::get('/jp-eksternal/tambah', [ExternalTrainingController::class, 'create'])->name('external-trainings.create');
    Route::post('/jp-eksternal', [ExternalTrainingController::class, 'store'])->name('external-trainings.store');
    Route::get('/jp-eksternal/{externalTraining}/file/{type}', [ExternalTrainingController::class, 'viewFile'])
        ->name('external-trainings.file')
        ->whereIn('type', ['certificate', 'supporting']);

    // Webinar & Live Sessions (KAK Poin 8, 9, 10, 12, 13, 14)
    Route::post('/webinars/{slug}/register', [WebinarController::class, 'register'])->name('webinars.register');
    Route::post('/webinars/{slug}/attendance', [WebinarController::class, 'recordAttendance'])->name('webinars.attendance');
    Route::get('/webinars/{slug}/materials/{material}', [WebinarController::class, 'downloadMaterial'])->name('webinars.materials.download');
    Route::post('/webinars/{slug}/evaluation', [WebinarController::class, 'submitEvaluation'])->name('webinars.evaluation');
    Route::get('/webinars/{slug}/certificate', [WebinarController::class, 'certificate'])->name('webinars.certificate');

    // Pengelolaan Profil Pengguna (KAK: Pengelolaan profil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');


    // Presensi Digital Peserta (KAK Presensi Digital: QR Code & Tombol Presensi)
    Route::post('/attendance/session/{session}/button', [AttendanceController::class, 'submitButton'])->name('attendance.button.submit');
    Route::get('/attendance/scan/{code}', [AttendanceController::class, 'scanQr'])->name('attendance.qr.scan');
    Route::post('/attendance/session/{session}/qr', [AttendanceController::class, 'submitQr'])->name('attendance.qr.submit');
    Route::post('/attendance/{attendance}/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');
    Route::get('/presensi', [AttendanceController::class, 'history'])->name('attendance.history');

    // Transkrip Resmi Pembelajaran ASN (Learning Record Transcript KAK XIV)
    Route::get('/learning-record/transcript', function (\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\Admin\ReportCenterController::class)->learningRecordTranscript(auth()->user(), $request);
    })->name('learning-record.my-transcript');

    // Pusat Notifikasi In-App (KAK Bab XXIII & Bab IX Poin 6)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/dropdown', [\App\Http\Controllers\NotificationController::class, 'dropdownData'])->name('notifications.dropdown');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Admin Panel Routes — Super Admin, BKPSDM, Admin OPD, & Narasumber/Instruktur
    Route::prefix('admin')->name('admin.')->middleware(['role:super_admin,admin,bkpsdm,admin_opd,narasumber,instructor', 'admin.access'])->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', function () { return redirect()->route('admin.dashboard'); });
        
        // Pusat Verifikasi & Approval Terpadu (Data ASN & JP Eksternal)
        Route::get('/verifications', [VerificationCenterController::class, 'index'])->name('verifications.index');
        
        // Courses & Lessons
        Route::get('/courses', [AdminController::class, 'courses'])->name('courses.index');
        Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
        Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
        Route::get('/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
        Route::post('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
        Route::post('/courses/{course}/approve', [AdminController::class, 'approveCourse'])->name('courses.approve');
        Route::get('/courses/{course}/lessons', [AdminController::class, 'lessons'])->name('courses.lessons');
        Route::post('/courses/{course}/lessons', [AdminController::class, 'storeLesson'])->name('courses.lessons.store');
        Route::delete('/courses/lessons/{lesson}', [AdminController::class, 'destroyLesson'])->name('courses.lessons.destroy');
        Route::get('/courses/{course}/certificate-builder', [AdminController::class, 'certificateBuilder'])->name('courses.certificate-builder');
        Route::post('/courses/{course}/certificate-builder', [AdminController::class, 'updateCertificateBuilder'])->name('courses.certificate-builder.update');

        // Manajemen Kategori & Taksonomi (WordPress Style)
        Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/import-excel', [\App\Http\Controllers\Admin\CategoryController::class, 'importExcel'])->name('categories.import-excel');

        // Manajemen User & Hak Akses (KAK: registrasi/login, profil, role, hak akses, reset password, aktivasi/nonaktif, log aktivitas)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/template/download', [UserController::class, 'downloadTemplate'])->name('users.template.download');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/import/csv', [UserController::class, 'importCsv'])->name('users.import.csv');
        Route::get('/users/verifications', [UserController::class, 'verifications'])->name('users.verifications.index');
        Route::post('/users/verifications/{changeRequest}/approve', [UserController::class, 'approveVerification'])->name('users.verifications.approve');
        Route::post('/users/verifications/{changeRequest}/reject', [UserController::class, 'rejectVerification'])->name('users.verifications.reject');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');

        // Pengelolaan Role (KAK: 6 Role)
        Route::get('/roles', [UserController::class, 'roles'])->name('roles.index');

        // Pengaturan Hak Akses (Permissions Matrix)
        Route::get('/permissions', [UserController::class, 'permissions'])->name('permissions.index');
        Route::post('/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');

        // Pencatatan Aktivitas Pengguna (Audit Trail)
        Route::get('/activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs.index');

        // Trainer Management
        Route::get('/trainers', [\App\Http\Controllers\Admin\TrainerController::class, 'index'])->name('trainers.index');
        Route::post('/trainers/{user}/approve', [\App\Http\Controllers\Admin\TrainerController::class, 'approve'])->name('trainers.approve');
        Route::post('/trainers/{user}/reject', [\App\Http\Controllers\Admin\TrainerController::class, 'reject'])->name('trainers.reject');

        // Homepage Sliders & Banners
        Route::get('/sliders', [AdminController::class, 'sliders'])->name('sliders.index');
        Route::post('/sliders/grid-layout', [AdminController::class, 'updateSliderGrid'])->name('sliders.grid-layout');
        Route::post('/sliders', [AdminController::class, 'storeSlider'])->name('sliders.store');
        Route::post('/sliders/add-course', [AdminController::class, 'addCourseToSlider'])->name('sliders.add-course');
        Route::post('/sliders/unselect-course', [AdminController::class, 'unselectCourseFromSlider'])->name('sliders.unselect-course');
        Route::post('/sliders/{slider}/toggle', [AdminController::class, 'toggleSlider'])->name('sliders.toggle');
        Route::post('/sliders/{slider}', [AdminController::class, 'updateSlider'])->name('sliders.update');
        Route::delete('/sliders/{slider}', [AdminController::class, 'deleteSlider'])->name('sliders.destroy');

        // Agenda & Webinars
        Route::get('/webinars', [AdminController::class, 'webinars'])->name('webinars.index');
        Route::post('/webinars', [AdminController::class, 'storeWebinar'])->name('webinars.store');
        Route::get('/webinars/{webinar}/detail', [AdminController::class, 'webinarDetail'])->name('webinars.detail');
        Route::post('/webinars/{webinar}', [AdminController::class, 'updateWebinar'])->name('webinars.update');
        Route::delete('/webinars/{webinar}', [AdminController::class, 'deleteWebinar'])->name('webinars.destroy');
        // Presensi (poin 9)
        Route::post('/webinars/{webinar}/attendance', [AdminController::class, 'markAttendance'])->name('webinars.attendance');
        Route::post('/webinars/{webinar}/attendance-all', [AdminController::class, 'markAllAttendance'])->name('webinars.attendance-all');
        // Materi (poin 10)
        Route::post('/webinars/{webinar}/materials', [AdminController::class, 'storeMaterial'])->name('webinars.materials.store');
        Route::delete('/webinar-materials/{material}', [AdminController::class, 'deleteMaterial'])->name('webinars.materials.destroy');

        // Promotional Popups
        Route::get('/popups', [AdminController::class, 'popups'])->name('popups.index');
        Route::post('/popups', [AdminController::class, 'storePopup'])->name('popups.store');
        Route::post('/popups/{popup}', [AdminController::class, 'updatePopup'])->name('popups.update');
        Route::post('/popups/{popup}/toggle', [AdminController::class, 'togglePopup'])->name('popups.toggle');

        // Assignment Submissions
        Route::get('/submissions', [AdminController::class, 'submissions'])->name('submissions.index');
        Route::post('/submissions/{submission}/grade', [AdminController::class, 'gradeSubmission'])->name('submissions.grade');

        // Konfigurasi Integrasi Single Sign-On (SSO)
        Route::get('/sso', [IntegrationController::class, 'ssoSettings'])->name('sso.index');
        Route::post('/sso', [IntegrationController::class, 'updateSsoSettings'])->name('sso.update');

        // Integrasi REST API SIMPEG Kabupaten (Plug & Play)
        Route::get('/simpeg', [SimpegController::class, 'index'])->name('simpeg.index');
        Route::post('/simpeg', [SimpegController::class, 'update'])->name('simpeg.update');
        Route::post('/simpeg/test', [SimpegController::class, 'testConnection'])->name('simpeg.test');
        Route::post('/simpeg/sync-all', [SimpegController::class, 'syncAll'])->name('simpeg.sync-all');
        Route::post('/simpeg/sync-nip', [SimpegController::class, 'syncByNip'])->name('simpeg.sync-nip');

        // Konfigurasi Email SMTP & Template Balasan Notifikasi (7 Skenario KAK)
        Route::get('/mail-settings', [\App\Http\Controllers\Admin\MailSettingController::class, 'index'])->name('mail.index');
        Route::post('/mail-settings/smtp', [\App\Http\Controllers\Admin\MailSettingController::class, 'updateSmtp'])->name('mail.update-smtp');
        Route::post('/mail-settings/test', [\App\Http\Controllers\Admin\MailSettingController::class, 'testConnection'])->name('mail.test');
        Route::post('/mail-settings/templates/{template}', [\App\Http\Controllers\Admin\MailSettingController::class, 'updateTemplate'])->name('mail.templates.update');
        Route::post('/mail-settings/templates/{template}/reset', [\App\Http\Controllers\Admin\MailSettingController::class, 'resetTemplate'])->name('mail.templates.reset');
        Route::get('/mail-settings/templates/{template}/preview', [\App\Http\Controllers\Admin\MailSettingController::class, 'previewTemplate'])->name('mail.templates.preview');
        Route::post('/mail-settings/send-reminder/webinar/{webinar}', [\App\Http\Controllers\Admin\MailSettingController::class, 'sendWebinarReminder'])->name('mail.send-webinar-reminder');

        // External ASN API Integrations & API Keys
        Route::get('/api-integrations', [IntegrationController::class, 'apiKeys'])->name('api.index');
        Route::post('/api-integrations', [IntegrationController::class, 'storeApiKey'])->name('api.store');
        Route::post('/api-integrations/google', [IntegrationController::class, 'updateGoogleSettings'])->name('api.google.update');
        Route::post('/api-integrations/payment', [IntegrationController::class, 'updatePaymentSettings'])->name('api.payment.update');
        Route::post('/api-integrations/{apiKey}/toggle', [IntegrationController::class, 'toggleApiKey'])->name('api.toggle');
        Route::delete('/api-integrations/{apiKey}', [IntegrationController::class, 'revokeApiKey'])->name('api.destroy');

        // Unified System Settings (Identity, Footer, JP ASN)
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/identity', [\App\Http\Controllers\Admin\SettingsController::class, 'updateIdentity'])->name('settings.identity.update');
        Route::post('/settings/footer', [\App\Http\Controllers\Admin\SettingsController::class, 'updateFooter'])->name('settings.footer.update');
        Route::post('/settings/jp', [\App\Http\Controllers\Admin\SettingsController::class, 'updateJp'])->name('settings.jp.update');
        Route::post('/settings/layout', [\App\Http\Controllers\Admin\SettingsController::class, 'updateLayout'])->name('settings.layout.update');
        Route::post('/settings/reset-logo', [\App\Http\Controllers\Admin\SettingsController::class, 'resetLogo'])->name('settings.reset-logo');
        Route::post('/settings/reset-favicon', [\App\Http\Controllers\Admin\SettingsController::class, 'resetFavicon'])->name('settings.reset-favicon');

        // Aliases for backwards-compatibility
        Route::get('/branding', fn() => redirect()->route('admin.settings.index', ['tab' => 'identity']))->name('branding.index');
        Route::post('/branding', [\App\Http\Controllers\Admin\SettingsController::class, 'updateIdentity'])->name('branding.update');
        Route::post('/branding/reset-logo', [\App\Http\Controllers\Admin\SettingsController::class, 'resetLogo'])->name('branding.reset-logo');
        Route::post('/branding/reset-favicon', [\App\Http\Controllers\Admin\SettingsController::class, 'resetFavicon'])->name('branding.reset-favicon');
        Route::get('/footer', fn() => redirect()->route('admin.settings.index', ['tab' => 'footer']))->name('footer.index');
        Route::post('/footer', [\App\Http\Controllers\Admin\SettingsController::class, 'updateFooter'])->name('footer.update');

        // External JP Review (Admin)
        Route::get('/jp-eksternal', [AdminController::class, 'externalTrainings'])->name('external-trainings.index');
        Route::post('/jp-eksternal/{externalTraining}/approve', [AdminController::class, 'approveExternalTraining'])->name('external-trainings.approve');
        Route::post('/jp-eksternal/{externalTraining}/reject', [AdminController::class, 'rejectExternalTraining'])->name('external-trainings.reject');

        // IDP (Individual Development Plan) Management
        Route::get('/idp', [IdpController::class, 'index'])->name('idp.index');
        Route::post('/idp', [IdpController::class, 'store'])->name('idp.store');
        Route::delete('/idp/all', [IdpController::class, 'destroyAll'])->name('idp.destroy-all');
        Route::delete('/idp/batch/{batchId}', [IdpController::class, 'destroyBatch'])->name('idp.destroy-batch');

        // Presensi Digital Management (KAK: QR Code, Tombol, Catatan Peserta, Kegiatan, Tanggal, Waktu, Status, Durasi)
        Route::get('/attendances', [AttendanceManagementController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/export/{session?}', [AttendanceManagementController::class, 'export'])->name('attendances.export');
        Route::get('/attendance-sessions', [AttendanceManagementController::class, 'sessions'])->name('attendances.sessions');
        Route::post('/attendance-sessions', [AttendanceManagementController::class, 'storeSession'])->name('attendances.sessions.store');
        Route::post('/attendance-sessions/{session}', [AttendanceManagementController::class, 'updateSession'])->name('attendances.sessions.update');
        Route::post('/attendance-sessions/{session}/toggle', [AttendanceManagementController::class, 'toggleSession'])->name('attendances.sessions.toggle');
        Route::get('/attendance-sessions/{session}/projector', [AttendanceManagementController::class, 'projector'])->name('attendances.projector');
        Route::get('/attendance-sessions/{session}/print', [AttendanceManagementController::class, 'print'])->name('attendances.print');
        Route::post('/attendance-sessions/{session}/manual', [AttendanceManagementController::class, 'manualStore'])->name('attendances.manual.store');
        Route::post('/attendances/{attendance}/status', [AttendanceManagementController::class, 'updateStatus'])->name('attendances.status.update');
        Route::delete('/attendances/{attendance}', [AttendanceManagementController::class, 'destroy'])->name('attendances.destroy');

        // Pencatatan & Rekapitulasi Jam Pelajaran (KAK: JP setiap kegiatan, per ASN, per OPD, periode, jenis pembelajaran, otomatis, ekspor)
        Route::get('/jp', [\App\Http\Controllers\Admin\JpReportController::class, 'index'])->name('jp.index');
        Route::get('/jp/asn', [\App\Http\Controllers\Admin\JpReportController::class, 'perAsn'])->name('jp.asn');
        Route::get('/jp/asn/{user}', [\App\Http\Controllers\Admin\JpReportController::class, 'detailAsn'])->name('jp.asn.detail');
        Route::get('/jp/opd', [\App\Http\Controllers\Admin\JpReportController::class, 'perOpd'])->name('jp.opd');
        Route::get('/jp/export/asn', [\App\Http\Controllers\Admin\JpReportController::class, 'exportAsn'])->name('jp.export.asn');
        Route::get('/jp/export/opd', [\App\Http\Controllers\Admin\JpReportController::class, 'exportOpd'])->name('jp.export.opd');
        Route::get('/jp/print', [\App\Http\Controllers\Admin\JpReportController::class, 'printReport'])->name('jp.print');

        // Pusat Pelaporan & Analytics & Report Builder Sederhana (KAK Bab XXII & XXXI)
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportCenterController::class, 'index'])->name('reports.index');
        Route::get('/reports/builder', [\App\Http\Controllers\Admin\ReportCenterController::class, 'builder'])->name('reports.builder');
        Route::get('/reports/export-excel', [\App\Http\Controllers\Admin\ReportCenterController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/print', [\App\Http\Controllers\Admin\ReportCenterController::class, 'printReport'])->name('reports.print');
        Route::get('/reports/learning-record/{targetUser}', [\App\Http\Controllers\Admin\ReportCenterController::class, 'learningRecordTranscript'])->name('reports.transcript');

        // OPD Activity Request — Alur Pengajuan & Approval Kegiatan OPD → BKPSDM (KAK Pasal VII)
        Route::get('/opd-requests', [OpdRequestController::class, 'index'])->name('opd-requests.index');
        Route::get('/opd-requests/create', [OpdRequestController::class, 'create'])->name('opd-requests.create');
        Route::post('/opd-requests', [OpdRequestController::class, 'store'])->name('opd-requests.store');
        Route::get('/opd-requests/{opdRequest}', [OpdRequestController::class, 'show'])->name('opd-requests.show');
        Route::get('/opd-requests/{opdRequest}/edit', [OpdRequestController::class, 'edit'])->name('opd-requests.edit');
        Route::post('/opd-requests/{opdRequest}', [OpdRequestController::class, 'update'])->name('opd-requests.update');
        Route::post('/opd-requests/{opdRequest}/approve', [OpdRequestController::class, 'approve'])->name('opd-requests.approve')->middleware('role:super_admin,admin,bkpsdm');
        Route::post('/opd-requests/{opdRequest}/reject', [OpdRequestController::class, 'reject'])->name('opd-requests.reject')->middleware('role:super_admin,admin,bkpsdm');
        Route::post('/opd-requests/{opdRequest}/revision', [OpdRequestController::class, 'requestRevision'])->name('opd-requests.revision')->middleware('role:super_admin,admin,bkpsdm');
        Route::delete('/opd-requests/{opdRequest}', [OpdRequestController::class, 'destroy'])->name('opd-requests.destroy');

        // Bank Soal & Import Massal (KAK VIII poin 14)
        Route::get('/question-banks', [QuestionBankController::class, 'index'])->name('question-banks.index');
        Route::post('/question-banks', [QuestionBankController::class, 'store'])->name('question-banks.store');
        Route::get('/question-banks/template', [QuestionBankController::class, 'downloadTemplate'])->name('question-banks.template');
        Route::get('/question-banks/{questionBank}', [QuestionBankController::class, 'show'])->name('question-banks.show');
        Route::post('/question-banks/{questionBank}', [QuestionBankController::class, 'update'])->name('question-banks.update');
        Route::delete('/question-banks/{questionBank}', [QuestionBankController::class, 'destroy'])->name('question-banks.destroy');
        Route::post('/question-banks/{questionBank}/questions', [QuestionBankController::class, 'storeQuestion'])->name('question-banks.questions.store');
        Route::delete('/question-banks/questions/{bankQuestion}', [QuestionBankController::class, 'destroyQuestion'])->name('question-banks.questions.destroy');
        Route::post('/question-banks/{questionBank}/import', [QuestionBankController::class, 'importCsv'])->name('question-banks.import');
        Route::post('/question-banks/{questionBank}/copy-to-quiz', [QuestionBankController::class, 'copyToQuiz'])->name('question-banks.copy-to-quiz');

        // Kalender Visibility Controls (Admin)
        Route::post('/calendar/toggle-visibility', [CalendarController::class, 'toggleVisibility'])->name('calendar.toggle-visibility');

        // Artikel, Regulasi & Repositori Pengetahuan (KAK XVI Knowledge Management)
        Route::get('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [\App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [\App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}/edit', [\App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('articles.edit');
        Route::post('/articles/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('articles.update');
        Route::post('/articles/{article}/toggle-status', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleStatus'])->name('articles.toggle-status');
        Route::delete('/articles/{article}', [\App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::post('/articles/attachments/{attachment}', [\App\Http\Controllers\Admin\ArticleController::class, 'updateAttachment'])->name('articles.attachments.update');
        Route::delete('/articles/attachments/{attachment}', [\App\Http\Controllers\Admin\ArticleController::class, 'deleteAttachment'])->name('articles.attachments.destroy');
    });
});


