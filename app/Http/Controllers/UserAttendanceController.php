<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attendance::query()
            ->with('user')
            ->userId($request->input('user_id'))
            ->since($request->get('since'), now()->subDays(30))
            ->until($request->get('until'), now())
            ->latest();
        $paginate = $request->boolean('paginate', true);

        return inertia('user-attendance/Index', [
            'attendances' => Inertia::defer(fn() => ! $paginate
                ? $query->get()->each->append('time')
                : $query->paginate()->through(fn($model) => $model->append('time'))->withQueryString()),
        ]);
    }
}
