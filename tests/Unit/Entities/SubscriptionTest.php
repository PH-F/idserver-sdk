<?php

namespace Tests\Unit\Entities;

use Carbon\Carbon;
use Tests\TestCase;
use Xingo\IDServer\Entities\Subscription;

class SubscriptionTest extends TestCase
{
    /** @test */
    public function it_can_check_if_it_is_active()
    {
        $active = new Subscription([
            'status' => 'active',
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);
        
        $expiring = new Subscription([
            'status' => 'expiring',
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        $this->assertTrue($active->isActive());
        $this->assertTrue($expiring->isActive());
    }

    /** @test */
    public function it_can_check_if_it_is_not_active()
    {
        $subscription = new Subscription([
            'status' => 'expired',
        ]);

        $this->assertFalse($subscription->isActive());
    }

        /** @test */
        public function it_can_check_active_on_the_dates()
        {
            $subscription = new Subscription([
                'status' => 'active',
                'start_date' => Carbon::now()->addDay(),
                'end_date' => Carbon::now()->addMonth(),
            ]);
    
            $this->assertFalse($subscription->isActive());
        }
}
