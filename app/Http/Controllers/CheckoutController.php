<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * Checkout a book for a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutBook(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                "user_id" => "required|exists:users,id",
                "book_id" => "required|exists:books,id",
            ]);

            // Find the book with the given book_id
            $book = Book::find($request->book_id);

            // Check if the book has available copies for checkout
            if ($book->copies <= 0) {
                return response()->json(
                    ["message" => "Book is not available for checkout"],
                    422
                );
            }

            // Get the current checkout date
            $checkoutDate = Carbon::now()->toDateString();

            // Create a new checkout entry
            $checkout = Checkout::create([
                "user_id" => $request->user_id,
                "book_id" => $request->book_id,
                "checkout_date" => $checkoutDate,
            ]);

            // Decrement the available copies of the book after successful checkout
            $book->decrement("copies");

            return response()->json([
                "message" => "Book checked out successfully",
                "data" => $checkout,
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                ["message" => "Validation failed", "errors" => $e->errors()],
                422
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Book not found"], 404);
        } catch (\Exception $e) {
            return response()->json(
                ["message" => "Something Went Wrong!"],
                500
            );
        }
    }

    /**
     * Return a book that was previously checked out.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnBook(Request $request, $id)
    {
        try {
            // Find the checkout entry with the given id
            $checkout = Checkout::findOrFail($id);

            // Check if the book has already been returned
            if ($checkout->return_date !== null) {
                return response()->json(
                    ["message" => "Book is already returned"],
                    422
                );
            }

            // Set the return date to the current date and save the changes
            $checkout->return_date = Carbon::now()->toDateString();
            $checkout->save();

            // Increment the available copies of the book after successful return
            $book = $checkout->book;
            $book->increment("copies");

            return response()->json([
                "message" => "Book returned successfully",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["message" => "Entry not found"], 404);
        } catch (\Exception $e) {
            return response()->json(
                ["message" => "Something Went Wrong!"],
                500
            );
        }
    }
}
