<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// Hapus use Inertia\Inertia; // Tidak lagi digunakan
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Tampilkan daftar item menggunakan Blade.
     */
    public function index(Request $request)
    {
        $items = $request->user()->items()
            ->latest()
            ->paginate(10); // Hapus withQueryString jika tidak diperlukan lagi

        // Kembalikan view Blade 'seller.items.index'
        return view('seller.items.index', compact('items'));
    }

    /**
     * Tampilkan form tambah item menggunakan Blade.
     */
    public function create()
    {
        // Kembalikan view Blade 'seller.items.create'
        return view('seller.items.create');
    }

    /**
     * Simpan item baru dari form Blade.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        $item = new Item($validated);
        $item->user_id = $request->user()->id;
        $item->unique_code = (string) Str::uuid();
        $item->save();

        // Redirect Blade standar
        return redirect()->route('seller.items.index')->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Tampilkan halaman edit menggunakan Blade.
     */
    public function edit(Item $item)
    {
        abort_if($item->user_id !== auth()->id(), 403);

        // Kembalikan view Blade 'seller.items.edit'
        return view('seller.items.edit', compact('item'));
    }

    /**
     * Proses update item dari form Blade.
     */
    public function update(Request $request, Item $item)
    {
        abort_if($item->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented',
        ]);

        $item->update($validated);

        // Redirect Blade standar
        return redirect()->route('seller.items.index')->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Hapus item.
     */
    public function destroy(Item $item)
    {
        abort_if($item->user_id !== auth()->id(), 403);
        $item->delete();
        // Redirect Blade standar
        return redirect()->route('seller.items.index')->with('success', 'Item berhasil dihapus.');
    }

    /**
     * Download QR Code sebagai PDF (menggunakan view Blade).
     */
    public function downloadQrPdf(Item $item)
    {
        abort_if($item->user_id !== auth()->id(), 403);
        // Pastikan view 'seller.items.qr_pdf' masih ada
        $pdf = Pdf::loadView('seller.items.qr_pdf', compact('item'));
        $fileName = 'qr-code-' . Str::slug($item->name) . '.pdf';
        return $pdf->download($fileName);
    }
}

