<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:userApi']);
    }

    /**
     * Display a listing of the contacts.
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::all();
        return response()->json(['success' => true, 'data' => $contacts], 200);
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'helpline_number' => 'required',
        ]);

        $contact = Contact::create($validated);

        return response()->json(['success' => true, 'message' => 'Contact created successfully', 'data' => $contact], 201);
    }

    /**
     * Display the specified contact.
     */
    public function show($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $contact], 200);
    }

    /**
     * Update the specified contact.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found'], 404);
        }

        $validated = $request->validate([
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'helpline_number' => 'required',
        ]);

        $contact->update($validated);

        return response()->json(['success' => true, 'message' => 'Contact updated successfully', 'data' => $contact], 200);
    }

    /**
     * Remove the specified contact.
     */
    public function destroy($id): JsonResponse
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found'], 404);
        }

        $contact->delete();

        return response()->json(['success' => true, 'message' => 'Contact deleted successfully'], 200);
    }
}
