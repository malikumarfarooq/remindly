<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Invoices ──
    Route::get('/invoices',                    [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',             [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/invoices/{invoice}',          [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit',     [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::delete('/invoices/{invoice}',       [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice}/pdf',      [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('/invoices/{invoice}/sent',    [InvoiceController::class, 'markAsSent'])->name('invoices.markSent');
    Route::post('/invoices/{invoice}/paid',    [InvoiceController::class, 'markAsPaid'])->name('invoices.markPaid');

    // ── Recurring Invoices ──
    Route::get('/recurring',                       [RecurringInvoiceController::class, 'index'])->name('recurring.index');
    Route::get('/recurring/create',                [RecurringInvoiceController::class, 'create'])->name('recurring.create');
    Route::get('/recurring/{recurring}/edit',      [RecurringInvoiceController::class, 'edit'])->name('recurring.edit');
    Route::post('/recurring/{recurring}/toggle',   [RecurringInvoiceController::class, 'togglePause'])->name('recurring.toggle');
    Route::delete('/recurring/{recurring}',        [RecurringInvoiceController::class, 'destroy'])->name('recurring.destroy');

    // ── Clients ──
    Route::get('/clients',               [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create',        [ClientController::class, 'create'])->name('clients.create');
    Route::get('/clients/{client}',      [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::delete('/clients/{client}',   [ClientController::class, 'destroy'])->name('clients.destroy');

    // ── Reminder Sequences ──
    Route::get('/reminders',                     [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('/reminders/create',              [ReminderController::class, 'create'])->name('reminders.create');
    Route::get('/reminders/{sequence}/edit',     [ReminderController::class, 'edit'])->name('reminders.edit');
    Route::delete('/reminders/{sequence}',       [ReminderController::class, 'destroy'])->name('reminders.destroy');

    // ── Still pending (coming soon) ──
    Route::get('/payments',  fn() => view('coming-soon'))->name('payments.index');
    Route::get('/ai',        fn() => view('coming-soon'))->name('ai.index');
    Route::get('/reports',   fn() => view('coming-soon'))->name('reports.index');
    Route::get('/settings',  fn() => view('coming-soon'))->name('settings.index');
});

// ── Public invoice view ──
Route::get('/i/{token}', [InvoiceController::class, 'publicShow'])->name('invoices.public');

require __DIR__ . '/auth.php';
