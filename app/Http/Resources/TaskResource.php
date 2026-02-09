<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'priority' => $this->priority,
            'status' => $this->status,
            'deadline' => $this->deadline->toIso8601String(),
            'is_recurring' => $this->is_recurring,
            'recurrence_type' => $this->recurrence_type,
            'remind_before_minutes' => $this->remind_before_minutes,
            'remind_via' => $this->remind_via,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'user' => new UserResource($this->whenLoaded('user')),
            'client' => new ClientResource($this->whenLoaded('client')),
        ];
    }
}
