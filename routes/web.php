<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Invoices (real controller, Livewire-powered) ──
    Route::get('/invoices',                    [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',             [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/invoices/{invoice}',          [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit',     [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::delete('/invoices/{invoice}',       [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice}/pdf',      [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/sent',    [InvoiceController::class, 'markAsSent'])->name('invoices.markSent');
    Route::post('/invoices/{invoice}/paid',    [InvoiceController::class, 'markAsPaid'])->name('invoices.markPaid');

    // ── Still pending (coming soon) ──
    Route::get('/recurring',       fn() => view('coming-soon'))->name('recurring.index');
    Route::get('/clients',         fn() => view('coming-soon'))->name('clients.index');
    Route::get('/reminders',       fn() => view('coming-soon'))->name('reminders.index');
    Route::get('/payments',        fn() => view('coming-soon'))->name('payments.index');
    Route::get('/ai',              fn() => view('coming-soon'))->name('ai.index');
    Route::get('/reports',         fn() => view('coming-soon'))->name('reports.index');
    Route::get('/settings',        fn() => view('coming-soon'))->name('settings.index');
});

// ── Public invoice view (no login required) ──
Route::get('/i/{token}', [InvoiceController::class, 'publicShow'])->name('invoices.public');

require __DIR__ . '/auth.php';
