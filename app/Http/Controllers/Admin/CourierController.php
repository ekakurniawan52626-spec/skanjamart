<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Delivery;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::query()
            ->withCount([
                'deliveries as active_deliveries_count' => fn ($q) => $q->whereIn('status', [Delivery::ASSIGNED, Delivery::ON_THE_WAY]),
                'deliveries as total_deliveries_count',
            ])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.couriers.index', compact('couriers'));
    }

    public function store(Request $request)
    {
        Courier::create($this->validated($request));

        return back()->with('success', 'Kurir ditambahkan.');
    }

    public function edit(Courier $courier)
    {
        return view('admin.couriers.edit', compact('courier'));
    }

    public function update(Request $request, Courier $courier)
    {
        $courier->update($this->validated($request));

        return redirect()->route('admin.couriers.index')->with('success', 'Data kurir diperbarui.');
    }

    public function destroy(Courier $courier)
    {
        if ($courier->deliveries()->exists()) {
            return back()->with('error', 'Kurir ini punya riwayat pengiriman, jadi tidak bisa dihapus. Nonaktifkan saja lewat menu Ubah.');
        }

        $courier->delete();

        return back()->with('success', 'Kurir dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'vehicle' => 'nullable|string|max:60',
        ], [
            'phone.regex' => 'No. HP hanya boleh berisi angka, spasi, tanda + dan -.',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
