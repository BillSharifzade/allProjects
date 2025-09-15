<?php

use App\Models\Loan;
use App\Models\Note;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\IndexController::class, 'index'])->name('index');
Route::get('/signin', [\App\Http\Controllers\SigninController::class, 'index'])->name('signin');
Route::post('/signin', [\App\Http\Controllers\SigninController::class, 'store'])->middleware('throttle:5,1');
Route::get('/signout', [\App\Http\Controllers\SignoutController::class, 'index'])->name('signout');

Route::middleware(['auth', 'cashier'])->group(function(){
    Route::get('/loans', [\App\Http\Controllers\Cashbox\LoanController::class, 'index'])->name('loans')->can('viewAny', Loan::class);
    Route::get('/loans/create', [\App\Http\Controllers\Cashbox\LoanController::class, 'create'])->can('create', Loan::class)->middleware('shift.open')->name('create-loan');
    Route::post('/loans/create', [\App\Http\Controllers\Cashbox\LoanController::class, 'store'])->can('create', Loan::class)->middleware('shift.open');
    Route::get('/loans/{loan}/update', [\App\Http\Controllers\Cashbox\LoanController::class, 'edit'])->can('update', 'loan')->name('edit-loan');
    Route::post('/loans/{loan}/update', [\App\Http\Controllers\Cashbox\LoanController::class, 'update'])->can('update', 'loan');
    Route::get('/loans/{loan}', [\App\Http\Controllers\Cashbox\LoanController::class, 'show'])->name('loan');
    Route::get('/loans/{loan}/close', [\App\Http\Controllers\Cashbox\LoanController::class, 'close']);
    Route::post('/loans/{loan}/close', [\App\Http\Controllers\Cashbox\LoanController::class, 'over']);

    Route::get('/payments', [\App\Http\Controllers\Cashbox\PaymentController::class, 'index'])->can('viewAny', Payment::class)->name('payments');
    Route::get('/payments/{loan}/create', [\App\Http\Controllers\Cashbox\PaymentController::class, 'create'])->can('create', Payment::class)->middleware('shift.open');
    Route::post('/payments/{loan}/create', [\App\Http\Controllers\Cashbox\PaymentController::class, 'store'])->can('create', Payment::class)->middleware('shift.open');
    Route::get('/loaners/{passportNumber}', [\App\Http\Controllers\Cashbox\LoanerController::class, 'show']);
    Route::get('/print/payment', [\App\Http\Controllers\Cashbox\PrintController::class, 'payment']);
    Route::get('/print/receipt', [\App\Http\Controllers\Cashbox\PrintController::class, 'receipt']);
    Route::get('/print/loan/{loan}', [\App\Http\Controllers\Cashbox\PrintController::class, 'loan']);
    Route::get('/print/withdrawal/{loan}', [\App\Http\Controllers\Cashbox\PrintController::class, 'withdrawal']);
    Route::get('/print/loanslip/{loan}', [\App\Http\Controllers\Cashbox\PrintController::class, 'loanslip']);
    Route::get('/print/sale', [\App\Http\Controllers\Cashbox\PrintController::class, 'sale']);

    Route::get('/gold-prices/json', [\App\Http\Controllers\Cashbox\GoldPriceController::class, 'json']);

    Route::get('/notes', [\App\Http\Controllers\Cashbox\NoteController::class, 'index']);
    Route::get('/notes/create', [\App\Http\Controllers\Cashbox\NoteController::class, 'create'])->can('create', Note::class);
    Route::post('/notes/create', [\App\Http\Controllers\Cashbox\NoteController::class, 'store'])->can('create', Note::class);

    Route::get('/notes/{note}/update', [\App\Http\Controllers\Cashbox\NoteController::class, 'edit'])->can('update', 'note');
    Route::post('/notes/{note}/update', [\App\Http\Controllers\Cashbox\NoteController::class, 'update'])->can('update', 'note');

    // Cashier shift management
    Route::post('/shift/open', [\App\Http\Controllers\Cashbox\ShiftController::class, 'open']);
    Route::post('/shift/close', [\App\Http\Controllers\Cashbox\ShiftController::class, 'close']);
    Route::get('/shift/report/{shift}', [\App\Http\Controllers\Cashbox\ShiftController::class, 'report']);

    // Cashier transfer funds
    Route::get('/transfer', [\App\Http\Controllers\Cashbox\TransferController::class, 'create'])->middleware('shift.open');
    Route::post('/transfer', [\App\Http\Controllers\Cashbox\TransferController::class, 'store'])->middleware('shift.open');

    // Cashier transfer history (daily)
    Route::get('/transfers', [\App\Http\Controllers\Cashbox\TransferHistoryController::class, 'index'])->name('cashier-transfers');

    // Cashier expenses
    Route::get('/expenses', [\App\Http\Controllers\Cashbox\ExpenseController::class, 'index'])->name('cashier-expenses');
    Route::get('/expenses/create', [\App\Http\Controllers\Cashbox\ExpenseController::class, 'create'])->middleware('shift.open');
    Route::post('/expenses/create', [\App\Http\Controllers\Cashbox\ExpenseController::class, 'store'])->middleware('shift.open');

    // Cashier sell loan
    Route::post('/sales/{loan}', [\App\Http\Controllers\Cashbox\SaleController::class, 'store'])->middleware('shift.open');
    Route::get('/sales', [\App\Http\Controllers\Cashbox\SaleHistoryController::class, 'index'])->name('cashier-sales');

    // Cashier incassation accept
    Route::get('/incassation/accept', [\App\Http\Controllers\Cashbox\IncassationAcceptController::class, 'index']);
    Route::post('/incassation/accept', [\App\Http\Controllers\Cashbox\IncassationAcceptController::class, 'accept']);
    Route::post('/incassation/not-delivered', [\App\Http\Controllers\Cashbox\IncassationAcceptController::class, 'notDelivered']);
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function(){
    Route::get('/loans', [\App\Http\Controllers\Admin\LoanController::class, 'index'])->can('viewAny', Loan::class)->name('admin-loans');
    Route::get('/loans/{loan}/delete', [\App\Http\Controllers\Admin\LoanController::class, 'delete'])->can('delete', 'loan');
    Route::get('/loans/{loan}/update', [\App\Http\Controllers\Admin\LoanController::class, 'edit'])->can('update', 'loan');
    Route::post('/loans/{loan}/update', [\App\Http\Controllers\Admin\LoanController::class, 'update'])->can('update', 'loan');
    // Removed closed loans list in favor of Archive
    Route::get('/loans/close-requests', [\App\Http\Controllers\Admin\LoanController::class, 'close_requests'])->can('viewAny', Loan::class)->name('admin-loans-close-requests');
    Route::get('/interest-rates', [\App\Http\Controllers\Admin\InterestRateController::class, 'index'])->name('interest-rates');
    Route::get('/interest-rates/{interestRate}/delete', [\App\Http\Controllers\Admin\InterestRateController::class, 'delete']);
    Route::get('/interest-rates/create', [\App\Http\Controllers\Admin\InterestRateController::class, 'create']);
    Route::post('/interest-rates/create', [\App\Http\Controllers\Admin\InterestRateController::class, 'store']);
    Route::get('/interest-rates/{interestRate}/update', [\App\Http\Controllers\Admin\InterestRateController::class, 'edit']);
    Route::post('/interest-rates/{interestRate}/update', [\App\Http\Controllers\Admin\InterestRateController::class, 'update']);

    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin-payments');
    Route::get('/payments/{loan}/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->can('create', Payment::class);
    Route::post('/payments/{loan}/create', [\App\Http\Controllers\Admin\PaymentController::class, 'store']);
    Route::get('/payments/{payment}/delete', [\App\Http\Controllers\Admin\PaymentController::class, 'delete'])->can('delete', 'payment');

    Route::get('/cashboxes', [\App\Http\Controllers\Admin\CashboxController::class, 'index'])->name('cashboxes');
    Route::get('/cashboxes/create', [\App\Http\Controllers\Admin\CashboxController::class, 'create']);
    Route::post('/cashboxes/create', [\App\Http\Controllers\Admin\CashboxController::class, 'store']);
    Route::get('/cashboxes/{cashbox}/update', [\App\Http\Controllers\Admin\CashboxController::class, 'edit']);
    Route::post('/cashboxes/{cashbox}/update', [\App\Http\Controllers\Admin\CashboxController::class, 'update']);
    Route::get('/cashboxes/{cashbox}/delete', [\App\Http\Controllers\Admin\CashboxController::class, 'delete']);

    Route::get('/cashbox-users', [\App\Http\Controllers\Admin\CashboxUserController::class, 'index'])->name('cashbox-users');
    Route::get('/cashbox-users/create', [\App\Http\Controllers\Admin\CashboxUserController::class, 'create']);
    Route::post('/cashbox-users/create', [\App\Http\Controllers\Admin\CashboxUserController::class, 'store']);
    Route::get('/cashbox-users/{cashboxUser}/delete', [\App\Http\Controllers\Admin\CashboxUserController::class, 'delete']);
    Route::get('/cashbox-users/{cashboxUser}/update', [\App\Http\Controllers\Admin\CashboxUserController::class, 'edit']);
    Route::post('/cashbox-users/{cashboxUser}/update', [\App\Http\Controllers\Admin\CashboxUserController::class, 'update']);
    Route::get('/cashbox-users/{cashboxUser}/reporter', [\App\Http\Controllers\Admin\CashboxUserController::class, 'reporter']);

    Route::get('/print/loan/{loan}', [\App\Http\Controllers\Admin\PrintController::class, 'loan']);

    Route::get('/gold-prices', [\App\Http\Controllers\Admin\GoldPriceController::class, 'index'])->name('gold-prices');
    Route::get('/gold-prices/create', [\App\Http\Controllers\Admin\GoldPriceController::class, 'create']);
    Route::post('/gold-prices/create', [\App\Http\Controllers\Admin\GoldPriceController::class, 'store']);
    Route::get('/gold-prices/{goldPrice}/update', [\App\Http\Controllers\Admin\GoldPriceController::class, 'edit']);
    Route::post('/gold-prices/{goldPrice}/update', [\App\Http\Controllers\Admin\GoldPriceController::class, 'update']);
    Route::get('/gold-prices/{goldPrice}/delete', [\App\Http\Controllers\Admin\GoldPriceController::class, 'delete']);

    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin-reports');
    Route::get('/reports/overdue65/form', [\App\Http\Controllers\Admin\ReportController::class, 'overdue65Form'])->name('admin-overdue65-form');
    Route::get('/reports/overdue65', [\App\Http\Controllers\Admin\ReportController::class, 'overdue65'])->name('admin-overdue65');
    // Admin monthly reports
    Route::get('/reports/monthly', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'index'])->name('admin-monthly-report');
    Route::get('/reports/monthly/export', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'export'])->name('admin-monthly-report-export');
    // Archive
    Route::get('/archive', [\App\Http\Controllers\Admin\ReportController::class, 'archive'])->name('admin-archive');
    Route::get('/gold-prices/json', [\App\Http\Controllers\Admin\GoldPriceController::class, 'json']);

    Route::get('/passwords', [\App\Http\Controllers\Admin\PasswordController::class, 'index'])->name('passwords');
    Route::post('/passwords', [\App\Http\Controllers\Admin\PasswordController::class, 'store']);

    Route::get('/notes', [\App\Http\Controllers\Admin\NoteController::class, 'index']);

    Route::get('/excel/cashbox', [\App\Http\Controllers\Admin\ExcelController::class, 'cashbox']);

    // Shifts admin view
    Route::get('/shifts', [\App\Http\Controllers\Admin\ShiftController::class, 'index'])->name('admin-shifts');

    // Admin transfer funds to cashier
    Route::get('/transfer', [\App\Http\Controllers\Admin\TransferController::class, 'create']);
    Route::post('/transfer', [\App\Http\Controllers\Admin\TransferController::class, 'store']);

    // Admin transfer history
    Route::get('/transfers', [\App\Http\Controllers\Admin\TransferHistoryController::class, 'index'])->name('admin-transfers');

    // Admin expense history
    Route::get('/expenses', [\App\Http\Controllers\Admin\ExpenseHistoryController::class, 'index'])->name('admin-expenses');
    Route::get('/expenses/{expense}/delete', [\App\Http\Controllers\Admin\ExpenseHistoryController::class, 'delete']);

    // Admin blacklist upload
    Route::get('/blacklist', [\App\Http\Controllers\Admin\BlacklistController::class, 'index'])->name('admin-blacklist');
    Route::post('/blacklist', [\App\Http\Controllers\Admin\BlacklistController::class, 'upload'])->name('admin-blacklist-upload');

    // Admin sales
    Route::get('/sales', [\App\Http\Controllers\Admin\SaleController::class, 'index'])->name('admin-sales');
    Route::get('/sales/{sale}/cancel', [\App\Http\Controllers\Admin\SaleController::class, 'cancel']);

    // Admin incassators
    Route::get('/incassators', [\App\Http\Controllers\Admin\IncassatorController::class, 'index'])->name('admin-incassators');
    Route::get('/incassators/create', [\App\Http\Controllers\Admin\IncassatorController::class, 'create']);
    Route::post('/incassators/create', [\App\Http\Controllers\Admin\IncassatorController::class, 'store']);
    Route::get('/incassators/{user}/delete', [\App\Http\Controllers\Admin\IncassatorController::class, 'delete']);
    Route::get('/incassators/{user}/safe', [\App\Http\Controllers\Admin\IncassatorController::class, 'safe']);

    // Admin incassation history
    Route::get('/incassation', [\App\Http\Controllers\Admin\IncassationHistoryController::class, 'index'])->name('admin-incassation');

    // Admin HR
    Route::get('/hr', [\App\Http\Controllers\Admin\HrController::class, 'index'])->name('admin-hr');
    Route::get('/hr/create', [\App\Http\Controllers\Admin\HrController::class, 'create']);
    Route::post('/hr/create', [\App\Http\Controllers\Admin\HrController::class, 'store']);
    Route::get('/hr/{employee}/edit', [\App\Http\Controllers\Admin\HrController::class, 'edit'])->name('admin-hr-edit');
    Route::post('/hr/{employee}/update', [\App\Http\Controllers\Admin\HrController::class, 'update']);
    Route::get('/hr/{employee}/delete', [\App\Http\Controllers\Admin\HrController::class, 'delete']);
    Route::post('/hr/{employee}/contracts', [\App\Http\Controllers\Admin\HrController::class, 'addContract']);

    // Admin dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin-dashboard');
    // Admin daily report list and download
    Route::get('/daily-report', [\App\Http\Controllers\Admin\DailyReportController::class, 'index'])->name('admin-daily-report');
    Route::get('/daily-report/{shift}/download', [\App\Http\Controllers\Admin\DailyReportController::class, 'download'])->name('admin-daily-report-download');
});

Route::prefix('reporter')->middleware(['auth', 'reporter'])->group(function(){
    Route::get('/loans', [\App\Http\Controllers\Reporter\LoanController::class, 'index'])->can('viewAny', Loan::class)->name('reporter-loans');
    Route::get('/loans/closed', [\App\Http\Controllers\Reporter\LoanController::class, 'closed'])->can('viewAny', Loan::class)->name('reporter-closed-loans');
    Route::get('/print/loan/{loan}', [\App\Http\Controllers\Reporter\PrintController::class, 'loan']);
    Route::get('/payments', [\App\Http\Controllers\Reporter\PaymentController::class, 'index'])->name('reporter-payments');
    Route::get('/reports', [\App\Http\Controllers\Reporter\ReportController::class, 'index'])->name('reporter-reports');
    Route::get('/reports/overdue65/form', [\App\Http\Controllers\Reporter\ReportController::class, 'overdue65Form'])->name('reporter-overdue65-form');
    Route::get('/reports/overdue65', [\App\Http\Controllers\Reporter\ReportController::class, 'overdue65'])->name('reporter-overdue65');
    // Reporter monthly reports
    Route::get('/reports/monthly', [\App\Http\Controllers\Reporter\MonthlyReportController::class, 'index'])->name('reporter-monthly-report');
    Route::get('/reports/monthly/export', [\App\Http\Controllers\Reporter\MonthlyReportController::class, 'export'])->name('reporter-monthly-report-export');
    Route::get('/excel/cashbox', [\App\Http\Controllers\Reporter\ExcelController::class, 'cashbox']);

    // Reporter transfer history
    Route::get('/transfers', [\App\Http\Controllers\Reporter\TransferHistoryController::class, 'index'])->name('reporter-transfers');

    // Reporter expense history
    Route::get('/expenses', [\App\Http\Controllers\Reporter\ExpenseHistoryController::class, 'index'])->name('reporter-expenses');

    // Reporter sales
    Route::get('/sales', [\App\Http\Controllers\Reporter\SaleController::class, 'index'])->name('reporter-sales');

    // Reporter daily report list and download
    Route::get('/daily-report', [\App\Http\Controllers\Reporter\DailyReportController::class, 'index'])->name('reporter-daily-report');
    Route::get('/daily-report/{shift}/download', [\App\Http\Controllers\Reporter\DailyReportController::class, 'download'])->name('reporter-daily-report-download');
});

// Incassator
Route::prefix('inc')->middleware(['auth','incassator'])->group(function(){
    Route::get('/safe', [\App\Http\Controllers\Incassator\SafeController::class, 'index'])->name('inc-safe');
    Route::get('/safe/create', [\App\Http\Controllers\Incassator\SafeController::class, 'create']);
    Route::post('/safe/create', [\App\Http\Controllers\Incassator\SafeController::class, 'store']);
    Route::get('/todeliver', [\App\Http\Controllers\Incassator\ToDeliverController::class, 'index'])->name('inc-todeliver');
    Route::post('/todeliver/pick', [\App\Http\Controllers\Incassator\ToDeliverController::class, 'pick']);
    Route::post('/todeliver/deliver', [\App\Http\Controllers\Incassator\ToDeliverController::class, 'deliver']);
    Route::get('/history', [\App\Http\Controllers\Incassator\HistoryController::class, 'index'])->name('inc-history');
});

