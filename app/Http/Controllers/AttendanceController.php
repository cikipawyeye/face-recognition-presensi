<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Services\FaceRecognitionService;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return inertia('attendance/Index', [
            'attendances' => Inertia::defer(function () use ($request) {
                $data = Attendance::where('user_id', $request->user()?->id)
                    ->latest()
                    ->paginate(15)
                    ->withQueryString();

                $data->getCollection()->transform(function ($item) {
                    return $item->append('time');
                });

                return $data;
            }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return inertia('attendance/Create', [
            'current' => Attendance::where('check_out', null)->where('user_id', $request->user()->id)->latest()->first(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        try {
            /** @disregard */
            /** @var \App\Models\User $user */
            $user = $request->user();
            $media = $user->getFirstMedia('face-reference');
            if ($media == null) {
                throw new \LogicException('User has not added any photos yet.');
            }

            /** @disregard */
            $response = FaceRecognitionService::recognize($request->file('photo'), $media);
            $body = $response->json();

            if (!$response->successful()) {
                $message = isset($body['detail']) ? $body['detail'] : 'Failed to detect face.';

                throw new \LogicException($message);
            }

            if (!isset($body['verified']) || !$body['verified']) {
                throw new \LogicException(isset($body['detail']) ? $body['detail'] : 'Pencocokan wajah gagal.');
            }

            if ($request->filled('attendance_id')) {
                $attendance = Attendance::where('id', $request->input('attendance_id'))->where('user_id', $user->id)->firstOrFail();
                $attendance->update([
                    'check_out' => now(),
                ]);
            } else {
                Attendance::create([
                    'user_id' => $user->id,
                    'check_in' => now(),
                ]);
            }

            return redirect()
                ->route('attendances.index')
                ->with('success', 'Attendance created successfully.');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withErrors([
                    'photo' => $th->getMessage()
                ]);
        }
    }
}
