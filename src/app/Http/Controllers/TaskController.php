<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyword = request('keyword');
        $sort = request('sort', 'id');
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $query = Task::with('deal.customer.user');

        if (Auth::user()->isSales()) {
            $query->whereHas('deal.customer', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $query->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($query) use ($keyword) {

                // タスク名
                $query->where('title', 'like', "%{$keyword}%")

                    // 商談名
                    ->orWhereHas('deal', function ($query) use ($keyword) {
                        $query->where('title', 'like', "%{$keyword}%");
                    })

                    // 顧客名
                    ->orWhereHas('deal.customer', function ($query) use ($keyword) {
                        $query->where('company_name', 'like', "%{$keyword}%");
                    })

                    // 担当営業
                    ->orWhereHas('deal.customer.user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
            });
        });

        if (in_array($sort, ['id', 'title', 'due_date', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $tasks = $query
            ->paginate(10)
            ->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deals = Auth::user()->isAdmin()
            ? Deal::orderBy('id')->get()
            : Deal::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        return view('tasks.create', compact('deals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        Task::create([
            'deal_id' => $request->deal_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'is_completed' => $request->boolean('is_completed'),
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'タスクを登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $deals = Auth::user()->isAdmin()
            ? Deal::orderBy('id')->get()
            : Deal::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        return view('tasks.edit', compact('task', 'deals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update([
            'deal_id' => $request->deal_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'is_completed' => $request->boolean('is_completed'),
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'タスクを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'タスクを削除しました。');
    }
}
