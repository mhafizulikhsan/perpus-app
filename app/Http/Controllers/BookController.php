<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Menampilkan daftar buku
    
public function index(Request $request)
{
    // Mengambil data buku berdasarkan kata kunci pencarian
    $books = Book::when($request->search, function ($query, $search) {
        // Mencari berdasarkan judul atau nama penulis
        $query->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%");
    })
    ->latest()
    ->paginate(10)
    ->withQueryString();

    // Mengirim data buku dan kata kunci pencarian ke halaman Vue
    return inertia('Books/Index', [
        'books' => $books,
        'search' => $request->search,
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
    // Mengambil data buku yang akan diedit
    // melalui Route Model Binding

    // Mengirim data buku ke halaman Vue Books/Edit
    // agar formulir menampilkan nilai lama
    return inertia('Books/Edit', [
        'book' => $book,
    ]);
}

public function update(Request $request, Book $book)
{
    // Validasi data yang dikirim dari formulir
    $validated = $request->validate([
        'kode_buku' => 'required|unique:books,kode_buku,' . $book->id,
        'judul' => 'required|max:255',
        'pengarang' => 'required|max:255',
        'penerbit' => 'nullable|max:255',
        'tahun' => 'nullable|digits:4',
        'stok' => 'required|integer|min:0',
    ]);

    // Memperbarui data buku dengan data baru
    $book->update($validated);

    // Kembali ke halaman daftar buku
    // dengan pesan bahwa data berhasil diperbarui
    return redirect()
        ->route('books.index')
        ->with('success', 'Buku berhasil diperbarui');
}


    // Menghapus buku
   
public function destroy(Book $book)
{
    // Menghapus data buku dari tabel books
    $book->delete();

    // Kembali ke halaman daftar buku
    // dengan pesan bahwa buku berhasil dihapus
    return redirect()
        ->route('books.index')
        ->with('success', 'Buku berhasil dihapus');
}

public function share(Request $request): array
{
    return [
        ...parent::share($request),

        // Membagikan informasi pengguna yang sedang login
        'auth' => [
            'user' => $request->user(),
        ],

        // Membagikan pesan flash ke seluruh halaman Vue
        'flash' => [
            // Membaca pesan sukses dari session
            'success' => session('success'),
        ],
    ];
}

}
