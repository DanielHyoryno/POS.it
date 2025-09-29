@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">

  @if (session('ok'))   <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div>@endif
  @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded"><ul class="list-disc pl-5 text-sm">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul></div>
  @endif

  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
    <div class="flex items-start gap-4">
      @if($item->image_path)
        <img src="{{ Storage::url($item->image_path) }}" class="h-20 w-20 rounded object-cover" alt="{{ $item->name }}">
      @else
        <div class="h-20 w-20 rounded bg-gray-200 grid place-items-center text-gray-500 text-xs">IMG</div>
      @endif
      <div class="min-w-0">
        <h1 class="text-2xl font-bold">{{ $item->name }}</h1>
        <div class="text-gray-600 text-sm">Base unit: <b>{{ $item->base_unit }}</b></div>
        <div class="text-gray-600 text-sm">Current stock: <b>{{ rtrim(rtrim(number_format($item->current_qty,3,'.',''), '0'), '.') }} {{ $item->base_unit }}</b></div>
        <div class="text-gray-600 text-sm">Low stock threshold: <b>{{ rtrim(rtrim(number_format($item->low_stock_threshold,3,'.',''), '0'), '.') }}</b></div>
        <div class="mt-2">
          <span class="px-2 py-0.5 rounded {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
            {{ $item->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
      </div>
      <div class="ml-auto">
        <form action="{{ route('admin.items.toggle',$item) }}" method="POST">@csrf @method('PATCH')
          <button class="px-3 py-2 text-sm border rounded">{{ $item->is_active ? 'Deactivate' : 'Activate' }}</button>
        </form>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Lots table --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-200">
      <div class="px-6 py-4 border-b bg-slate-50">
        <h2 class="font-semibold">Lots</h2>
        <p class="text-sm text-gray-500">FEFO: earliest expiry consumed first.</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-600 uppercase tracking-wide">
            <tr>
              <th class="text-left p-3.5">Expiry</th>
              <th class="text-right p-3.5">Qty</th>
              <th class="text-left p-3.5">Note</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($item->lots()->orderByRaw('expiry_date IS NULL')->orderBy('expiry_date')->get() as $lot)
              @php $expired = $lot->expiry_date && $lot->expiry_date->lte(now()); @endphp
              <tr class="{{ $expired ? 'bg-rose-50' : '' }}">
                <td class="p-3.5">
                  @if($lot->expiry_date)
                    {{ $lot->expiry_date->format('Y-m-d') }}
                    @if(!$expired)
                      <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                        {{ now()->diffInDays($lot->expiry_date,false) }}d left
                      </span>
                    @else
                      <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Expired</span>
                    @endif
                  @else
                    <span class="text-gray-500">No expiry</span>
                  @endif
                </td>
                <td class="p-3.5 text-right">
                  {{ rtrim(rtrim(number_format($lot->qty,3,'.',''), '0'), '.') }} {{ $item->base_unit }}
                </td>
                <td class="p-3.5">{{ $lot->note }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="p-6 text-center text-gray-500">No lots yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Stock actions --}}
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
        <h3 class="font-semibold mb-3">Restock</h3>
        <form action="{{ route('admin.items.restock',$item) }}" method="POST" class="space-y-3">
          @csrf
          <div class="grid grid-cols-2 gap-3">
            <input type="number" name="qty" step="0.001" min="0.001" class="border p-2 rounded" placeholder="Qty" required>
            <select name="unit" class="border p-2 rounded" required>
              <option value="g">g</option><option value="kg">kg</option>
              <option value="ml">ml</option><option value="L">L</option>
              <option value="pcs">pcs</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Expiry Date (optional)</label>
            <input type="date" name="expiry_date" class="border p-2 rounded w-full">
          </div>
          <input type="text" name="note" class="border p-2 rounded w-full" placeholder="Note (optional)">
          <button class="w-full px-3 py-2 bg-emerald-600 text-white rounded">Add Stock</button>
        </form>
      </div>

      <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 p-6">
        <h3 class="font-semibold mb-3">Adjust (+/−)</h3>
        <form action="{{ route('admin.items.adjust',$item) }}" method="POST" class="space-y-3">
          @csrf
          <div class="grid grid-cols-2 gap-3">
            <input type="number" name="qty" step="0.001" class="border p-2 rounded" placeholder="+10 or -5" required>
            <select name="unit" class="border p-2 rounded" required>
              <option value="g">g</option><option value="kg">kg</option>
              <option value="ml">ml</option><option value="L">L</option>
              <option value="pcs">pcs</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Expiry (only for +)</label>
            <input type="date" name="expiry_date" class="border p-2 rounded w-full">
          </div>
          <input type="text" name="note" class="border p-2 rounded w-full" placeholder="Reason (optional)">
          <button class="w-full px-3 py-2 bg-slate-700 text-white rounded">Apply Adjust</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Movements (recent) --}}
  <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b bg-slate-50">
      <h2 class="font-semibold">Recent Movements</h2>
    </div>
    <div class="divide-y">
      @foreach($movements as $mv)
        <div class="px-6 py-3 flex items-center justify-between">
          <div>
            <div class="text-sm text-gray-500">{{ $mv->created_at->format('Y-m-d H:i') }} • {{ ucfirst($mv->reason) }}</div>
            @if($mv->note) <div class="text-sm">{{ $mv->note }}</div> @endif
          </div>
          <div class="font-mono {{ $mv->change_qty < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
            {{ $mv->change_qty > 0 ? '+' : '' }}
            {{ rtrim(rtrim(number_format($mv->change_qty,3,'.',''), '0'), '.') }} {{ $item->base_unit }}
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
