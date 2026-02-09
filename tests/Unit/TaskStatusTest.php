<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function test_pending_can_transition_to_in_progress_and_cancelled(): void
    {
        $status = TaskStatus::Pending;

        $this->assertTrue($status->canTransitionTo(TaskStatus::InProgress));
        $this->assertTrue($status->canTransitionTo(TaskStatus::Cancelled));
        $this->assertFalse($status->canTransitionTo(TaskStatus::Done)); 
    }

    public function test_in_progress_can_transition_to_done_and_cancelled(): void
    {
        $status = TaskStatus::InProgress;

        $this->assertTrue($status->canTransitionTo(TaskStatus::Done));
        $this->assertTrue($status->canTransitionTo(TaskStatus::Cancelled));
        $this->assertFalse($status->canTransitionTo(TaskStatus::Pending));
    }

    public function test_done_is_final_state(): void
    {
        $status = TaskStatus::Done;

        $this->assertFalse($status->canTransitionTo(TaskStatus::Pending));
        $this->assertFalse($status->canTransitionTo(TaskStatus::InProgress));
        $this->assertFalse($status->canTransitionTo(TaskStatus::Cancelled));
        $this->assertTrue($status->isFinal());
    }

    public function test_cancelled_is_final_state(): void
    {
        $status = TaskStatus::Cancelled;

        $this->assertTrue($status->isFinal());
        $this->assertFalse($status->canTransitionTo(TaskStatus::Pending));
    }
}
