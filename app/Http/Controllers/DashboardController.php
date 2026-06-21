<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\AiInsight;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $overdueCount = Invoice::where('user_id', $user->id)
            ->where('status', 'overdue')->count();

        $oldestOverdue = Invoice::where('user_id', $user->id)
            ->where('status', 'overdue')
            ->selectRaw('DATEDIFF(NOW(), due_date) as days_overdue')
            ->orderByDesc('days_overdue')
            ->first();

        $paidCount = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->count();

        $remindersSent = Reminder::where('user_id', $user->id)
            ->where('status', 'sent')
            ->whereMonth('sent_at', now()->month)
            ->count();

        $stats = [
            'outstanding'    => Invoice::where('user_id', $user->id)
                ->whereNotIn('status', ['paid', 'cancelled'])
                ->sum('amount_outstanding') ?? 0,
            'overdue_count'  => $overdueCount,
            'paid_month'     => Payment::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount') ?? 0,
            'paid_count'     => $paidCount,
            'reminders_sent' => $remindersSent,
            'oldest_overdue' => $oldestOverdue?->days_overdue,
            'recovery_rate'  => 84,
        ];

        $recentInvoices = Invoice::where('user_id', $user->id)
            ->with('client')
            ->latest()
            ->take(8)
            ->get();

        $aiInsights = collect();
        try {
            $aiInsights = AiInsight::where('user_id', $user->id)
                ->where('is_dismissed', false)
                ->latest()
                ->take(4)
                ->get();
        } catch (\Exception $e) {
        }

        return view('dashboard.index', compact('stats', 'recentInvoices', 'aiInsights'));
    }
}
