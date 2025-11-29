<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class ItemController extends Controller
{
    use AuthorizesRequests; // Gunakan trait otorisasi

    /**
     * Menampilkan daftar item milik seller yang sedang login (dengan paginasi).
     * Rute: GET /api/items
     */
    public function index(Request $request)
    {
        $items = $request->user()->items()
            ->latest()
            ->paginate(15); // Paginasi akan otomatis diformat sebagai JSON

        return response()->json($items);
    }

    /**
     * Menyimpan item baru ke database.
     * Rute: POST /api/items
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $item = new Item($validated);
        $item->user_id = $request->user()->id;
        $item->unique_code = (string) Str::uuid(); // Generate kode unik
        $item->save();

        return response()->json([
            'message' => 'Item berhasil ditambahkan.',
            'item' => $item,
        ], 201); // 201 Created
    }

    /**
     * Menampilkan detail satu item.
     * Rute: GET /api/items/{item}
     */
    public function show(Item $item)
    {
        // Otorisasi: Pastikan seller hanya bisa melihat item miliknya
        $this->authorize('view', $item);

        return response()->json($item);
    }

    /**
     * Mengupdate data item.
     * Rute: PUT/PATCH /api/items/{item}
     */
    public function update(Request $request, Item $item)
    {
        // Otorisasi: Pastikan seller hanya bisa mengupdate item miliknya
        $this->authorize('update', $item);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item->update($validator->validated());

        return response()->json([
            'message' => 'Item berhasil diperbarui.',
            'item' => $item,
        ]);
    }

    /**
     * Menghapus item.
     * Rute: DELETE /api/items/{item}
     */
    public function destroy(Item $item)
    {
        // Otorisasi: Pastikan seller hanya bisa menghapus item miliknya
        $this->authorize('delete', $item);
        
        $item->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus.'
        ], 200); // atau 204 No Content
    }
}