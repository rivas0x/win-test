<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $database = config('database.connections.mysql.database');

        \Artisan::call('migrate');
    }

    public function test_can_show(): void
    {
        $response = $this->json('GET', '/api/orders/1');

        $response->assertStatus(200)->assertJson([
            'status' => true
        ]);
    }

    public function test_can_list(): void
    {
        $filter = array(
            'status' => 'ASC',
            'group_id' => 'DESC',
            'amount' => 'ASC',
        );

        $response = $this->json('GET', '/api/orders', $filter);

        $response->assertStatus(200)->assertJson([
            'status' => true
        ]);
    }

    public function test_cannot_list(): void
    {
        $filter = array(
            'status' => 'ASCd'
        );

        $response = $this->json('GET', '/api/orders', $filter);

        $response->assertStatus(400)->assertJson([
            'status' => false
        ]);
    }

    public function test_can_totals(): void
    {
        $response = $this->json('GET', '/api/orders_totals');

        $response->assertStatus(200)->assertJson([
            'status' => true
        ]);
    }

    public function test_can_pdf(): void
    {
        $response = $this->json('GET', '/api/orders_pdf');

        $response->assertStatus(200)->assertHeader('Content-Type', 'application/pdf');
    }
}
