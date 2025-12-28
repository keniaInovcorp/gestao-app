<?php

namespace App\Http\Controllers;

use App\Models\CalendarAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarActionController extends Controller
{
    /**
     * Display a listing of calendar actions.
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->checkPermission('calendar-actions.read');

        $calendarActions = CalendarAction::orderBy('name')->paginate(15);

        return Inertia::render('Configurations/CalendarActions/Index', [
            'calendarActions' => $calendarActions,
        ]);
    }

    /**
     * Show the form for creating a new calendar action.
     *
     * @return Response
     */
    public function create(): Response
    {
        $this->checkPermission('calendar-actions.create');

        return Inertia::render('Configurations/CalendarActions/Create');
    }

    /**
     * Store a newly created calendar action in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission('calendar-actions.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        CalendarAction::create($validated);

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação de calendário criada com sucesso');
    }

    /**
     * Display the specified calendar action.
     *
     * @param CalendarAction $calendarAction
     * @return Response
     */
    public function show(CalendarAction $calendarAction): Response
    {
        $this->checkPermission('calendar-actions.read');

        return Inertia::render('Configurations/CalendarActions/Show', [
            'calendarAction' => $calendarAction,
        ]);
    }

    /**
     * Show the form for editing the specified calendar action.
     *
     * @param CalendarAction $calendarAction
     * @return Response
     */
    public function edit(CalendarAction $calendarAction): Response
    {
        $this->checkPermission('calendar-actions.update');

        return Inertia::render('Configurations/CalendarActions/Edit', [
            'calendarAction' => $calendarAction,
        ]);
    }

    /**
     * Update the specified calendar action in storage.
     *
     * @param Request $request
     * @param CalendarAction $calendarAction
     * @return RedirectResponse
     */
    public function update(Request $request, CalendarAction $calendarAction): RedirectResponse
    {
        $this->checkPermission('calendar-actions.update');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $calendarAction->update($validated);

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação de calendário atualizada com sucesso');
    }

    /**
     * Remove the specified calendar action from storage.
     *
     * @param CalendarAction $calendarAction
     * @return RedirectResponse
     */
    public function destroy(CalendarAction $calendarAction): RedirectResponse
    {
        $this->checkPermission('calendar-actions.delete');

        $eventsCount = $calendarAction->events()->count();
        if ($eventsCount > 0) {
            return redirect()->route('calendar-actions.index')
                ->with('error', "Não pode eliminar esta ação porque está associada a {$eventsCount} evento(s).");
        }

        $calendarAction->delete();

        return redirect()->route('calendar-actions.index')
            ->with('success', 'Ação de calendário eliminada com sucesso');
    }
}
