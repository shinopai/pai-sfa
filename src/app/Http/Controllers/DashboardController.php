<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $customers = Customer::query();
        $deals = Deal::query();
        $tasks = Task::query();

        if ($user->isSales()) {
            $customers->where('user_id', $user->id);

            $deals->whereHas('customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

            $tasks->whereHas('deal.customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $customerCount = $customers->count();

        $dealCount = $deals->count();

        $incompleteTaskCount = (clone $tasks)
            ->where('is_completed', false)
            ->count();

        $recentDeals = (clone $deals)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        $todayTasks = (clone $tasks)
            ->with('deal.customer')
            ->whereDate('due_date', today())
            ->where('is_completed', false)
            ->orderBy('priority')
            ->get();

        return view('dashboard', compact(
            'customerCount',
            'dealCount',
            'incompleteTaskCount',
            'recentDeals',
            'todayTasks',
        ));
    }
}
