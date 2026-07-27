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
        $keyword = request('keyword');
        $sort = request('sort', 'id');
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $query = Activity::with(['deal.customer.user']);

        if (Auth::user()->isSales()) {
            $query->whereHas('deal.customer', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $query->when($keyword, function ($query) use ($keyword) {
            $query->where(function ($query) use ($keyword) {

                // 商談名
                $query->whereHas('deal', function ($query) use ($keyword) {
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

        if (in_array($sort, ['id', 'activity_date', 'created_at'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('id');
        }

        $activities = $query
            ->paginate(10)
            ->withQueryString();

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
