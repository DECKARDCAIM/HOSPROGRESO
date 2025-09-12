<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $year = $request->query('year', date('Y'));
        $search = $request->query('search');
        $page = (int) ($request->query('page', 1));
        $ttl = now()->addMinutes(10);
        $cacheKey = "holidays:index:v3:status={$status}:year={$year}:q=" . urlencode((string) $search) . ":p={$page}";

        $holidays = Cache::tags(['holidays'])->remember($cacheKey, $ttl, function () use ($status, $year, $search) {
            return Holiday::select('id', 'name', 'date', 'description', 'is_recurring', 'is_active', 'created_by')
                ->with('createdBy:id,name')
                ->where('is_active', $status === 'active' ? 1 : 0)
                ->whereYear('date', $year)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                          ->orWhere('description', 'like', "%$search%");
                    });
                })
                ->orderBy('date')
                ->paginate(25);
        });

        $holidays->appends($request->all());
        
        // Obtener años disponibles donde hay días festivos
        $availableYears = Cache::tags(['holidays'])->remember('holidays:available-years', now()->addHours(1), function () {
            return Holiday::selectRaw('YEAR(date) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        });
        
        return view('modules.holidays.index', compact('holidays', 'status', 'year', 'search', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('modules.holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
        ];
        $messages = [
            'name.required' => 'El nombre del día festivo es obligatorio.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe ser válida.',
        ];

        $this->validate($request, $rules, $messages);

        $holiday = new Holiday();
        $holiday->name = $request->input('name');
        $holiday->date = $request->input('date');
        $holiday->description = $request->input('description');
        $holiday->is_recurring = $request->boolean('is_recurring');
        $holiday->is_active = true; // Siempre activo por defecto
        $holiday->created_by = Auth::id();
        $holiday->save();

        Cache::tags(['holidays'])->flush();
        Cache::forget('holidays:available-years');

        return redirect()
            ->route('holidays.index', ['status' => 'active', 'year' => $holiday->date->year])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Creación Éxitosa',
                'message' => 'El día festivo ' . $holiday->name . ' se ha creado correctamente.'
            ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Holiday $holiday)
    {
        return view('modules.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
        ];
        $messages = [
            'name.required' => 'El nombre del día festivo es obligatorio.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe ser válida.',
        ];

        $this->validate($request, $rules, $messages);

        $holiday->name = $request->input('name');
        $holiday->date = $request->input('date');
        $holiday->description = $request->input('description');
        $holiday->is_recurring = $request->boolean('is_recurring');
        // is_active se maneja desde el index con eliminación lógica
        $holiday->save();

        Cache::tags(['holidays'])->flush();
        Cache::forget('holidays:available-years');

        return redirect()
            ->route('holidays.index', ['status' => 'active', 'year' => $holiday->date->year])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Actualización Éxitosa',
                'message' => 'El día festivo ' . $holiday->name . ' se ha actualizado correctamente.'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holidayName = $holiday->name;
        $holidayYear = $holiday->date->year;
        $holiday->is_active = false;
        $holiday->save();

        Cache::tags(['holidays'])->flush();
        Cache::forget('holidays:available-years');

        return redirect()
            ->route('holidays.index', ['status' => 'active', 'year' => $holidayYear])
            ->with('toast', [
                'type' => 'warning',
                'title' => 'Eliminación Éxitosa',
                'message' => 'El día festivo ' . $holidayName . ' se ha eliminado correctamente.'
            ]);
    }

    /**
     * Toggle active status
     */
    public function reactivate($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->is_active = true;
        $holiday->save();

        Cache::tags(['holidays'])->flush();
        Cache::forget('holidays:available-years');

        return redirect()
            ->route('holidays.index', ['status' => 'inactive', 'year' => $holiday->date->year])
            ->with('toast', [
                'type' => 'success',
                'title' => 'Reactivación Éxitosa',
                'message' => 'El día festivo ' . $holiday->name . ' ha sido reactivado correctamente.'
            ]);
    }

    /**
     * API endpoint para verificar si una fecha es día festivo
     */
    public function checkHoliday(Request $request)
    {
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['error' => 'Fecha requerida'], 400);
        }

        $isHoliday = Holiday::isHoliday($date);
        
        return response()->json([
            'is_holiday' => $isHoliday,
            'date' => $date
        ]);
    }

    /**
     * API endpoint para obtener días festivos en un rango
     */
    public function getHolidaysInRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Fechas de inicio y fin requeridas'], 400);
        }

        $holidays = Holiday::getHolidaysInRange($startDate, $endDate);
        
        return response()->json([
            'holidays' => $holidays->map(function($holiday) {
                return [
                    'id' => $holiday->id,
                    'name' => $holiday->name,
                    'date' => $holiday->date,
                    'description' => $holiday->description,
                    'is_recurring' => $holiday->is_recurring,
                ];
            })
        ]);
    }
}
