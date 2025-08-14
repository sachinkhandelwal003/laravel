<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AddOnBook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddOnBookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:userApi']);
    }

    /**
     * Display a listing of the AddOnBook.
     */
    public function index(): JsonResponse
    {
        $addonbooks = AddOnBook::all()->map(function ($addonbook) {
            return [
                'id' => $addonbook->id,
                'add_on_id' => $addonbook->add_on_id,
                'date' => date('Y-m-d', $addonbook->date),
                'time' => date('H:i', $addonbook->time),
            ];
        });

        return response()->json(['success' => true, 'data' => $addonbooks], 200);
    }

    /**
     * Store a newly created AddOnBook.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'add_on_id' => 'required|exists:add_ons,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $addonbook = AddOnBook::create([
            'add_on_id' => $request->add_on_id,
            'date' => strtotime($request->date),
            'time' => strtotime($request->time),
        ]);

        return response()->json(['success' => true, 'message' => 'Add-On Book created', 'data' => $addonbook], 201);
    }

    /**
     * Display the specified AddOnBook.
     */
    public function show($id): JsonResponse
    {
        $addonbook = AddOnBook::find($id);

        if (!$addonbook) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $addonbook->id,
                'add_on_id' => $addonbook->add_on_id,
                'date' => date('Y-m-d', $addonbook->date),
                'time' => date('H:i', $addonbook->time),
            ]
        ]);
    }

    /**
     * Update the specified AddOnBook.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $addonbook = AddOnBook::find($id);

        if (!$addonbook) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $request->validate([
            'add_on_id' => 'required|exists:add_ons,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $addonbook->update([
            'add_on_id' => $request->add_on_id,
            'date' => strtotime($request->date),
            'time' => strtotime($request->time),
        ]);

        return response()->json(['success' => true, 'message' => 'Add-On Book updated', 'data' => $addonbook]);
    }

    /**
     * Remove the specified AddOnBook.
     */
    public function destroy($id): JsonResponse
    {
        $addonbook = AddOnBook::find($id);

        if (!$addonbook) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $addonbook->delete();

        return response()->json(['success' => true, 'message' => 'Add-On Book deleted']);
    }
}
