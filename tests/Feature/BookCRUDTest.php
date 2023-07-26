<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Book;
use App\Models\User;


class BookCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /** @test */
    public function it_returns_all_books()
    {
        $books = Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'author', 'isbn', 'published_at', 'copies']]]);
    }

    /** @test */
    public function it_creates_a_new_book()
    {
        $bookData = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '1234567890',
            'published_at' => '2023-01-01',
            'copies' => 5,
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Book Added Successfully'])
            ->assertJsonStructure(['data' => ['id', 'title', 'author', 'isbn', 'published_at', 'copies']]);
    }

    /** @test */
    public function it_requires_all_fields_to_create_a_new_book()
    {
        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn', 'published_at', 'copies']);
    }

    /** @test */
    public function it_updates_an_existing_book()
    {
        $book = Book::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'isbn' => '0987654321',
            'published_at' => '2022-12-31',
            'copies' => 10,
        ];

        $response = $this->putJson("/api/books/{$book->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('books', $updatedData);
    }

    /** @test */
    public function it_deletes_an_existing_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book Deleted Successfully']);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_book()
    {
        $response = $this->putJson("/api/books/999", [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'isbn' => '0987654321',
            'published_at' => '2022-12-31',
            'copies' => 10,
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Book not found']);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_book()
    {
        $response = $this->deleteJson("/api/books/999");

        $response->assertStatus(404)
            ->assertJson(['message' => 'Book not found']);
    }

}
