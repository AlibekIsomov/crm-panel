<?php

namespace Tests\Unit\Modules\Delivery;

use App\Modules\Delivery\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_can_transition_from_pending_to_paid()
    {
        $status = OrderStatus::PENDING;
        $this->assertTrue($status->canTransitionTo(OrderStatus::PAID));
    }

    public function test_can_transition_from_pending_to_cancelled()
    {
        $status = OrderStatus::PENDING;
        $this->assertTrue($status->canTransitionTo(OrderStatus::CANCELLED));
    }

    public function test_cannot_transition_from_pending_to_delivered()
    {
        $status = OrderStatus::PENDING;
        $this->assertFalse($status->canTransitionTo(OrderStatus::DELIVERED));
    }

    public function test_cannot_transition_from_cancelled_to_any_status()
    {
        $status = OrderStatus::CANCELLED;
        $this->assertFalse($status->canTransitionTo(OrderStatus::PENDING));
        $this->assertFalse($status->canTransitionTo(OrderStatus::PAID));
    }

    public function test_label_returns_string()
    {
        $this->assertEquals('Ожидает оплаты', OrderStatus::PENDING->label());
        $this->assertEquals('Доставлен', OrderStatus::DELIVERED->label());
    }
}
