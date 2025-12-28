<?php

namespace App\Http\Controllers;

use App\Models\CalendarType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarTypeController extends Controller
{
    /**
     * Display a listing of calendar types.
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->checkPermission('calendar-types.read');

        $calendarTypes = CalendarType::orderBy('name')->paginate(15);

        return Inertia::render('Configurations/CalendarTypes/Index', [
            'calendarTypes' => $calendarTypes,
        ]);
    }

    /**
     * Show the form for creating a new calendar type.
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->checkPermission('calendar-types.create');

        return Inertia::render('Configurations/CalendarTypes/Create');
    }

    /**
     * Store a newly created calendar type in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission('calendar-types.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        CalendarType::create($validated);

        return redirect()->route('calendar-types.index')
            ->with('success', 'Tipo de calendário criado com sucesso');
    }

    /**
     * Display the specified calendar type.
     *
     * @param CalendarType $calendarType
     * @return Response
     */
    public function show(CalendarType $calendarType): Response
    {
        $this->checkPermission('calendar-types.read');

        return Inertia::render('Configurations/CalendarTypes/Show', [
            'calendarType' => $calendarType,
        ]);
    }

    /**
     * Show the form for editing the specified calendar type.
     *
     * @param CalendarType $calendarType
     * @return Response
     */
    public function edit(CalendarType $calendarType): Response
    {
        $this->checkPermission('calendar-types.update');

        return Inertia::render('Configurations/CalendarTypes/Edit', [
            'calendarType' => $calendarType,
        ]);
    }

    /**
     * Update the specified calendar type in storage.
     *
     * @param Request $request
     * @param CalendarType $calendarType
     * @return RedirectResponse
     */
    public function update(Request $request, CalendarType $calendarType): RedirectResponse
    {
        $this->checkPermission('calendar-types.update');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $calendarType->update($validated);

        return redirect()->route('calendar-types.index')
            ->with('success', 'Tipo de calendário atualizado com sucesso');
    }

    /**
     * Remove the specified calendar type from storage.
     *
     * @param CalendarType $calendarType
     * @return RedirectResponse
     */
    public function destroy(CalendarType $calendarType): RedirectResponse
    {
        $this->checkPermission('calendar-types.delete');

        $eventsCount = $calendarType->events()->count();
        if ($eventsCount > 0) {
            return redirect()->route('calendar-types.index')
                ->with('error', "Não pode eliminar este tipo porque está associado a {$eventsCount} evento(s).");
        }

        $calendarType->delete();

        return redirect()->route('calendar-types.index')
            ->with('success', 'Tipo de calendário eliminado com sucesso');
    }
}
