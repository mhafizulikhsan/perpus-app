
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

// Menerima data buku dan kata kunci pencarian dari controller
const props = defineProps({
    books: Object,
    search: String,
});

// Mengakses pesan flash dari Inertia
const page = usePage();

// Mengisi kotak pencarian dengan kata kunci sebelumnya
const keyword = ref(props.search ?? '');

// Fungsi pencarian buku
function cari() {
    router.get(
        route('books.index'),
        {
            search: keyword.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}

// Fungsi menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <Head title="Data Buku" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-7xl">
            <!-- Judul halaman -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Data Buku
                </h1>

                <p class="mt-1 text-gray-600">
                    Daftar buku perpustakaan.
                </p>
            </div>

            <!-- Pesan sukses -->
            <div
                v-if="page.props.flash?.success"
                class="mb-4 rounded-md bg-green-100 p-3 text-green-700"
            >
                {{ page.props.flash.success }}
            </div>

            <!-- Tombol tambah dan kotak pencarian -->
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <Link
                    href="/books/create"
                    class="rounded-md bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700"
                >
                    + Tambah Buku
                </Link>

                <input
                    v-model="keyword"
                    @keyup.enter="cari"
                    type="text"
                    placeholder="Cari judul atau pengarang..."
                    class="w-full max-w-md rounded-md border border-gray-300 p-3"
                />

                <button
                    type="button"
                    @click="cari"
                    class="rounded-md bg-gray-700 px-4 py-3 font-semibold text-white hover:bg-gray-800"
                >
                    Cari
                </button>

                <button
                    v-if="keyword"
                    type="button"
                    @click="keyword = ''; cari()"
                    class="rounded-md bg-gray-200 px-4 py-3 text-gray-800 hover:bg-gray-300"
                >
                    Reset
                </button>
            </div>

            <!-- Tabel data buku -->
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3">Kode</th>
                            <th class="border p-3">Judul</th>
                            <th class="border p-3">Pengarang</th>
                            <th class="border p-3">Stok</th>
                            <th class="border p-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in books.data"
                            :key="book.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border p-3">
                                {{ book.kode_buku }}
                            </td>

                            <td class="border p-3">
                                {{ book.judul }}
                            </td>

                            <td class="border p-3">
                                {{ book.penulis }}
                            </td>

                            <td class="border p-3">
                                {{ book.stok }}
                            </td>

                            <td class="border p-3">
                                <div class="flex flex-wrap gap-2">
                                    <Link
                                        :href="route('books.edit', book.id)"
                                        class="rounded bg-yellow-500 px-3 py-1 text-white hover:bg-yellow-600"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        @click="hapus(book.id)"
                                        class="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Tampilan ketika data kosong -->
                        <tr v-if="books.data.length === 0">
                            <td
                                colspan="5"
                                class="border p-5 text-center text-gray-500"
                            >
                                Data buku tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Informasi jumlah buku -->
            <p class="mt-4 text-sm text-gray-600">
                Total buku: {{ books.total }}
            </p>

            <!-- Navigasi halaman -->
            <div
                v-if="books.links && books.links.length > 3"
                class="mt-4 flex flex-wrap gap-2"
            >
                <template
                    v-for="(link, index) in books.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="rounded border px-3 py-2"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'bg-white text-gray-700': !link.active,
                        }"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="rounded border px-3 py-2 text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
