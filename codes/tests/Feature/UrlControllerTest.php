<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('urls.index'));

        $response->assertStatus(200);
        $response->assertViewIs('urls.index');
    }

    /**
     * Test the create method.
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->get(route('urls.create'));

        $response->assertStatus(200);
        $response->assertViewIs('urls.create');
    }

    /**
     * Test the store method.
     *
     * @return void
     */
    public function testStore()
    {
        $this->withoutMiddleware();

        $data = [
            'urls' => "https://example.com\nhttps://another-example.com",
        ];

        $response = $this->post(route('urls.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Creation successful',
        ]);
    }
}
