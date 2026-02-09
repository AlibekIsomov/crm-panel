<?php

namespace App\Http\Requests;

use App\Enums\RecurrenceType;
use App\Enums\ReminderChannel;
use App\Enums\TaskPriority;
use App\Enums\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'type' => ['required', Rule::enum(TaskType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'deadline' => ['required', 'date', 'after:now'],
            'is_recurring' => ['boolean'],
            'recurrence_type' => ['nullable', 'required_if:is_recurring,true', Rule::enum(RecurrenceType::class)],
            'remind_before_minutes' => ['nullable', 'integer', 'min:1'],
            'remind_via' => ['nullable', 'required_with:remind_before_minutes', Rule::enum(ReminderChannel::class)],
        ];
    }
}
