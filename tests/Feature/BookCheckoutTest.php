<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Book;
use App\Models\Checkout;

class BookCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }
    
    /** @test */
    public function testBookCheckout()
    {
        $book = Book::factory()->create(['copies' => 2]);

        $data = [
            'user_id' => auth()->user()->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/checkout', $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book checked out successfully']);

        $this->assertDatabaseHas('checkouts', $data);

        $book->refresh();
        $this->assertEquals(1, $book->copies);
    }

    /** @test */
    public function testBookReturn()
    {
        $book = Book::factory()->create(['copies' => 0]);
        $checkout = Checkout::create([
            'user_id' => auth()->user()->id,
            'book_id' => $book->id,
            'checkout_date' => now(),
        ]);

        $response = $this->putJson("/api/checkout/{$checkout->id}", []);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book returned successfully']);

        $checkout->refresh();
        $this->assertNotNull($checkout->return_date);

        $book = $checkout->book;
        $book->refresh();
        $this->assertEquals(1, $book->copies);
    }

    /** @test */
    public function testBookCheckoutNotAvailable()
    {
        $book = Book::factory()->create(['copies' => 0]);

        $data = [
            'user_id' => auth()->user()->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/checkout', $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Book is not available for checkout']);
    }
}
