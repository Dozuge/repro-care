<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Models\User;
use Illuminate\Http\Request;

class MenstruationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Index
    public function index()
    {
        $search = request('search');

        $query = Cycle::with('woman')
            ->orderBy('period_start_date', 'desc');

        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('woman', function($womanQuery) use ($search) {
                    $womanQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                });
            });
        }

        $records = $query->paginate(10);

        return view('midwife.menstruation.index', compact('records'));
    }

    // Create
    public function create($userId = null)
    {
        $woman = null;
        if ($userId) {
            $woman = User::where('role', 'user')->findOrFail($userId);
        }

        $women = User::where('role', 'user')->where('status', 'approved')->get();

        return view('midwife.menstruation.create', compact('women', 'woman'));
    }

    // Store
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        Cycle::create([
            'user_id' => $request->user_id,
            'period_start_date' => $request->start_date,
            'period_end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('midwife.menstruation.index')
            ->with('success', 'Menstruation record added successfully');
    }

    // Show
    public function show($id)
    {
        $record = Cycle::with('woman')->findOrFail($id);
        return view('midwife.menstruation.show', compact('record'));
    }

    // Edit
    public function edit($id)
    {
        $record = Cycle::findOrFail($id);
        $women = User::where('role', 'user')->where('status', 'approved')->get();

        return view('midwife.menstruation.edit', compact('record', 'women'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $record = Cycle::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $record->update([
            'user_id' => $request->user_id,
            'period_start_date' => $request->start_date,
            'period_end_date' => $request->end_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('midwife.menstruation.index')
            ->with('success', 'Menstruation record updated successfully');
    }

    // Delete
    public function destroy($id)
    {
        $record = Cycle::findOrFail($id);
        $record->delete();

        return redirect()->route('midwife.menstruation.index')
            ->with('success', 'Menstruation record deleted successfully');
    }

    // Woman Records
    public function womanRecords($userId)
    {
        $woman = User::where('role', 'user')->findOrFail($userId);
        $records = $woman->cycles()
            ->orderBy('period_start_date', 'desc')
            ->get();

        return view('midwife.menstruation.woman', compact('woman', 'records'));
    }
}
