<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Entity;
use App\Models\ContactFunction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts.
     *
     * @return Response
     */
    public function index(): Response
    {
        $contacts = Contact::with(['entity', 'contactFunction'])
            ->orderBy('number', 'desc')
            ->paginate(15);

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Show the form for creating a new contact.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Contacts/Create', [
            'entities' => Entity::where('status', 'active')->orderBy('name')->get(),
            'contactFunctions' => ContactFunction::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created contact in storage.
     *
     * @param StoreContactRequest $request
     * @return RedirectResponse
     */
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $lastContact = Contact::orderBy('number', 'desc')->first();
        $nextNumber = $lastContact ? (int)$lastContact->number + 1 : 1;

        Contact::create([
            'number' => str_pad($nextNumber, 6, '0', STR_PAD_LEFT),
            'entity_id' => $request->entity_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'function_id' => $request->function_id,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'gdpr_consent' => $request->gdpr_consent ?? false,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto criado com sucesso');
    }

    /**
     * Display the specified contact.
     *
     * @param Contact $contact
     * @return Response
     */
    public function show(Contact $contact): Response
    {
        $contact->load(['entity', 'contactFunction']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified contact.
     *
     * @param Contact $contact
     * @return Response
     */
    public function edit(Contact $contact): Response
    {
        return Inertia::render('Contacts/Edit', [
            'contact' => $contact->load(['entity', 'contactFunction']),
            'entities' => Entity::where('status', 'active')->orderBy('name')->get(),
            'contactFunctions' => ContactFunction::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified contact in storage.
     *
     * @param UpdateContactRequest $request
     * @param Contact $contact
     * @return RedirectResponse
     */
    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $data = $request->validated();
        unset($data['gdpr_consent']);
        
        $contact->update($data);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto atualizado com sucesso');
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param Contact $contact
     * @return RedirectResponse
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto eliminado com sucesso');
    }
}
