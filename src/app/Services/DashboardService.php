<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        $customerQuery = $this->customerQuery();
        $dealQuery = $this->dealQuery();
        $taskQuery = $this->taskQuery();
        // $activityQuery = $this->activityQuery();

        return [
            'customerCount' => $customerQuery->count(),

            'dealCount' => $dealQuery->count(),

            'incompleteTaskCount' => (clone $taskQuery)
                ->where('is_completed', false)
                ->count(),

            'recentDeals' => (clone $dealQuery)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get(),

            'todayTasks' => (clone $taskQuery)
                ->with('deal.customer')
                ->whereDate('due_date', today())
                ->where('is_completed', false)
                ->orderBy('priority')
                ->get(),

            'monthlyDeals' => $this->monthlyDeals(),

            'dealStatus' => $this->dealStatus(),

            'monthlyActivities' => $this->monthlyActivities(),

            'taskCompletion' => $this->taskCompletion()
        ];
    }

    private function customerQuery(): Builder
    {
        $query = Customer::query();

        $user = Auth::user();

        if ($user->isSales()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    private function dealQuery(): Builder
    {
        $query = Deal::query();

        $user = Auth::user();

        if ($user->isSales()) {
            $query->whereHas('customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }

    private function taskQuery(): Builder
    {
        $query = Task::query();

        $user = Auth::user();

        if ($user->isSales()) {
            $query->whereHas('deal.customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }

    private function activityQuery(): Builder
    {
        $query = Activity::query();

        $user = Auth::user();

        if ($user->isSales()) {
            $query->whereHas('deal.customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        return $query;
    }

    private function monthlyDeals(): array
    {
        $results = $this->dealQuery()
            ->selectRaw('EXTRACT(MONTH FROM created_at) AS month')
            ->selectRaw('COUNT(*) AS total')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = "{$month}月";
            $data[] = (int) ($results[$month] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function dealStatus(): array
    {
        $results = $this->dealQuery()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => [
                '新規',
                '提案中',
                '交渉中',
                '受注',
                '失注',
            ],
            'data' => [
                (int) ($results['new'] ?? 0),
                (int) ($results['proposal'] ?? 0),
                (int) ($results['negotiating'] ?? 0),
                (int) ($results['won'] ?? 0),
                (int) ($results['lost'] ?? 0),
            ],
        ];
    }

    private function monthlyActivities(): array
    {
        $results = $this->activityQuery()
            ->selectRaw('EXTRACT(MONTH FROM activity_date) AS month')
            ->selectRaw('COUNT(*) AS total')
            ->whereYear('activity_date', now()->year)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM activity_date)'))
            ->orderBy(DB::raw('EXTRACT(MONTH FROM activity_date)'))
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = "{$month}月";
            $data[] = (int) ($results[$month] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function taskCompletion(): array
    {
        $taskQuery = $this->taskQuery();

        $completed = (clone $taskQuery)
            ->where('is_completed', true)
            ->count();

        $incomplete = (clone $taskQuery)
            ->where('is_completed', false)
            ->count();

        return [
            'labels' => [
                '完了',
                '未完了',
            ],
            'data' => [
                $completed,
                $incomplete,
            ],
        ];
    }
}
