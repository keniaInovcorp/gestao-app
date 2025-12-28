<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of calendar events with filters.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $this->checkPermission('calendar-events.read');

        $currentUserId = Auth::id();

        $query = CalendarEvent::with(['entity', 'type', 'action', 'user'])
            ->where('status', 'active')
            ->where(function ($q) use ($currentUserId) {
                $q->where('share', true)
                  ->orWhere('user_id', $currentUserId);
            });

        if ($request->has('user_id') && $request->user_id) {
            $query->where(function ($q) use ($request, $currentUserId) {
                $q->where('user_id', $request->user_id)
                  ->where(function ($subQ) use ($currentUserId) {
                      $subQ->where('share', true)
                           ->orWhere('user_id', $currentUserId);
                  });
            });
        }

        if ($request->has('entity_id') && $request->entity_id) {
            $query->where('entity_id', $request->entity_id);
        }

        $events = $query->orderBy('date')->orderBy('time')->get();

        $eventsForCalendar = $events->map(function ($event) {
            $time = is_string($event->time) ? $event->time : $event->time->format('H:i:s');
            $start = $event->date->format('Y-m-d') . 'T' . $time;
            $endDateTime = $event->date->copy();
            $timeParts = explode(':', $time);
            $endDateTime->setTime((int)$timeParts[0], (int)$timeParts[1], (int)($timeParts[2] ?? 0));
            $endDateTime->addMinutes($event->duration);
            $end = $endDateTime->format('Y-m-d\TH:i:s');

            return [
                'id' => $event->id,
                'title' => $event->description ?: ($event->type?->name ?? 'Evento'),
                'start' => $start,
                'end' => $end,
                'allDay' => false,
                'extendedProps' => [
                    'entity' => $event->entity?->name,
                    'type' => $event->type?->name,
                    'action' => $event->action?->name,
                    'user' => $event->user->name,
                    'share' => $event->share,
                    'knowledge' => $event->knowledge,
                    'description' => $event->description,
                ],
            ];
        });

        $users = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $entities = Entity::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Calendar/Index', [
            'events' => $eventsForCalendar,
            'users' => $users,
            'entities' => $entities,
            'filters' => [
                'user_id' => $request->user_id,
                'entity_id' => $request->entity_id,
            ],
        ]);
    }

    /**
     * Show the form for creating a new calendar event.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $this->checkPermission('calendar-events.create');

        $types = \App\Models\CalendarType::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $actions = \App\Models\CalendarAction::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $entities = Entity::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $users = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Calendar/Create', [
            'types' => $types,
            'actions' => $actions,
            'entities' => $entities,
            'users' => $users,
            'defaultDate' => $request->query('date'),
        ]);
    }

    /**
     * Store a newly created calendar event in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission('calendar-events.create');

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1'],
            'share' => ['boolean'],
            'knowledge' => ['boolean'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'type_id' => ['nullable', 'exists:calendar_types,id'],
            'action_id' => ['nullable', 'exists:calendar_actions,id'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        CalendarEvent::create($validated);

        return redirect()->route('calendar-events.index')
            ->with('success', 'Evento criado com sucesso');
    }

    /**
     * Display the specified calendar event.
     *
     * @param CalendarEvent $calendarEvent
     * @return Response
     */
    public function show(CalendarEvent $calendarEvent): Response
    {
        $this->checkPermission('calendar-events.read');

        $calendarEvent->load(['entity', 'type', 'action', 'user']);

        return Inertia::render('Calendar/Show', [
            'event' => $calendarEvent,
        ]);
    }

    /**
     * Show the form for editing the specified calendar event.
     *
     * @param CalendarEvent $calendarEvent
     * @return Response
     */
    public function edit(CalendarEvent $calendarEvent): Response
    {
        $this->checkPermission('calendar-events.update');

        $types = \App\Models\CalendarType::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $actions = \App\Models\CalendarAction::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $entities = Entity::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $users = User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Calendar/Edit', [
            'event' => $calendarEvent,
            'types' => $types,
            'actions' => $actions,
            'entities' => $entities,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified calendar event in storage.
     *
     * @param Request $request
     * @param CalendarEvent $calendarEvent
     * @return RedirectResponse
     */
    public function update(Request $request, CalendarEvent $calendarEvent): RedirectResponse
    {
        $this->checkPermission('calendar-events.update');

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1'],
            'share' => ['boolean'],
            'knowledge' => ['boolean'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'type_id' => ['nullable', 'exists:calendar_types,id'],
            'action_id' => ['nullable', 'exists:calendar_actions,id'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $calendarEvent->update($validated);

        return redirect()->route('calendar-events.index')
            ->with('success', 'Evento atualizado com sucesso');
    }

    /**
     * Remove the specified calendar event from storage.
     *
     * @param CalendarEvent $calendarEvent
     * @return RedirectResponse
     */
    public function destroy(CalendarEvent $calendarEvent): RedirectResponse
    {
        $this->checkPermission('calendar-events.delete');

        $calendarEvent->delete();

        return redirect()->route('calendar-events.index')
            ->with('success', 'Evento eliminado com sucesso');
    }
}
