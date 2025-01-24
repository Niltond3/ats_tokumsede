<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Http\Resources\ReminderResource;
use App\Enums\ReminderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReminderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
{
    $clientId = $request->query('client_id');

    $reminders = Reminder::where('id_cliente', $clientId)
        ->where('status', ReminderStatus::ATIVO)
        ->paginate(10);

    return ReminderResource::collection($reminders);
}

public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'descricao' => 'required|string|max:1000',
        'data_limite' => 'nullable|date|after:today',
        'id_cliente' => 'required|exists:cliente,id',
    ]);

    $reminder = Reminder::create([
        'id_cliente' => $validated['id_cliente'],
        'descricao' => $validated['descricao'],
        'data_limite' => $validated['data_limite'] ?? null,
        'data_criacao' => now(),
        'status' => ReminderStatus::ATIVO
    ]);

    return response()->json(new ReminderResource($reminder), 201);
}

    public function show(Reminder $reminder): JsonResponse
    {
        $this->authorize('view', $reminder);

        return response()->json(new ReminderResource($reminder));
    }

    public function update(Request $request, Reminder $reminder): JsonResponse
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate([
            'descricao' => 'sometimes|string|max:1000',
            'data_limite' => 'sometimes|date|after:today',
            'status' => 'sometimes|in:ATIVO,INATIVO'
        ]);

        $reminder->update($validated);

        return response()->json(new ReminderResource($reminder));
    }

    public function destroy(Reminder $reminder): JsonResponse
    {
        $this->authorize('delete', $reminder);

        $reminder->status = ReminderStatus::EXCLUIDO;
        $reminder->save();
        $reminder->delete();

        return response()->json(null, 204);
    }
}
