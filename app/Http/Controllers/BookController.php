<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return response()->json(['data' => $books]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books|max:255',
                'published_at' => 'required|date',
                'copies' => 'required|integer|min:0',
            ]);

            $book = Book::create($request->all());
            return response()->json(['message' => 'Book Added Successfully', 'data' => $book], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|max:255|unique:books,isbn,' . $id,
                'published_at' => 'required|date',
                'copies' => 'required|integer|min:0',
            ]);

            $book = Book::findOrFail($id);
            $book->update($request->all());

            return response()->json(['message' => 'Book Updated Successfully', 'data' => $book], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Book not found'], 404);
        }catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(['message' => 'Book Deleted Successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Book not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something Went Wrong!'], 500);
        }
    }
}
