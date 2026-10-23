<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Menampilkan daftar buku
    public function index()
    {
        $books = Book::latest()->paginate(10);

        return inertia('Books/Index', [
            'books' => $books,
        ]);
    }

    // Menampilkan formulir tambah buku
    public function create()
    {
        return inertia('Books/Create');
    }

    // Menyimpan buku baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|string|max:255|unique:books,kode_buku',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'required|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        // Menyesuaikan nama input dengan kolom database
        $validated['penulis'] = $validated['pengarang'];
        $validated['tahun_terbit'] = $validated['tahun'];

        // Menghapus nama input yang tidak digunakan database
        unset($validated['pengarang'], $validated['tahun']);

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan');
    }

    // Menampilkan detail buku
    public function show(Book $book)
    {
        return inertia('Books/Show', [
            'book' => $book,
        ]);
    }

    // Menampilkan formulir edit buku
    public function edit(Book $book)
    {
        return inertia('Books/Edit', [
            'book' => $book,
        ]);
    }

    // Memperbarui data buku
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'kode_buku' => 'required|string|max:255|unique:books,kode_buku,' . $book->id,
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'required|digits:4',
            'stok' => 'required|integer|min:0',
        ]);

        // Menyesuaikan nama input dengan kolom database
        $validated['penulis'] = $validated['pengarang'];
        $validated['tahun_terbit'] = $validated['tahun'];

        unset($validated['pengarang'], $validated['tahun']);

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil diperbarui');
    }

    // Menghapus buku
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus');
    }
}
