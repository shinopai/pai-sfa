<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * 一覧表示
     */
    public function index()
    {
        $query = Activity::with(['deal.customer', 'deal.user']);

        if (Auth::user()->isSales()) {
            $query->whereHas('deal', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $activities = $query
            ->orderBy('id')
            ->paginate(10);

        return view('activities.index', compact('activities'));
    }

    /**
     * 登録画面表示
     */
    public function create()
    {
        $deals = Auth::user()->isAdmin()
            ? Deal::orderBy('id')->get()
            : Deal::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        return view('activities.create', compact('deals'));
    }

    /**
     * 登録処理
     */
    public function store(StoreActivityRequest $request)
    {
        Activity::create([
            'deal_id' => $request->deal_id,
            'activity_type' => $request->activity_type,
            'activity_date' => $request->activity_date,
            'content' => $request->content,
        ]);

        return redirect()
            ->route('activities.index')
            ->with('success', '営業活動を登録しました。');
    }

    /**
     * 詳細表示
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * 編集画面表示
     */
    public function edit(Activity $activity)
    {
        $deals = Auth::user()->isAdmin()
            ? Deal::orderBy('id')->get()
            : Deal::where('user_id', Auth::id())
            ->orderBy('id')
            ->get();

        return view('activities.edit', compact('activity', 'deals'));
    }

    /**
     * 更新処理
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $activity->update([
            'deal_id' => $request->deal_id,
            'activity_type' => $request->activity_type,
            'activity_date' => $request->activity_date,
            'content' => $request->content,
        ]);

        return redirect()
            ->route('activities.index')
            ->with('success', '営業活動を更新しました。');
    }

    /**
     * 削除処理
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', '営業活動を削除しました。');
    }
}
