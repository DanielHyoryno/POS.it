@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Items</h1>
    <div class="space-x-2">
      <a href="{{ route('admin.items.index', ['low' => 1]) }}" class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded">Low Stock</a>
      <a href="{{ route('admin.items.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New Item</a>
    </div>
  </div>

  @include('partials.flash')

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left p-3">Name</th>
          <th class="text-left p-3">Unit</th>
          <th class="text-right p-3">Stock</th>
          <th class="text-right p-3">Threshold</th>
          <th class="text-center p-3">Status</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($items as $it)
          <tr class="border-t">
            <td class="p-3 flex items-center gap-2">
              @if($it->image_path)
                <img src="{{ Storage::url($it->image_path) }}" class="h-8 w-8 rounded object-cover">
              @endif
              <a href="{{ route('admin.items.show',$it) }}" class="text-blue-600 hover:underline">{{ $it->name }}</a>
            </td>

            <td class="p-3">
              <a href="{{ route('admin.items.show', $it) }}" class="text-blue-600 hover:underline">{{ $it->name }}</a>
              @if ($it->current_qty <= $it->low_stock_threshold)
                <span class="ml-2 text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded">Low</span>
              @endif
            </td>
            <td class="p-3">{{ $it->base_unit }}</td>
            <td class="p-3 text-right">{{ rtrim(rtrim(number_format($it->current_qty,3,'.',''), '0'), '.') }}</td>
            <td class="p-3 text-right">{{ rtrim(rtrim(number_format($it->low_stock_threshold,3,'.',''), '0'), '.') }}</td>
            <td class="p-3 text-center">
              <span class="px-2 py-0.5 rounded {{ $it->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                {{ $it->is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="p-3 text-right space-x-2">
              <a href="{{ route('admin.items.edit', $it) }}" class="px-2 py-1 text-sm border rounded">Edit</a>
              <form action="{{ route('admin.items.toggle', $it) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                <button class="px-2 py-1 text-sm border rounded">{{ $it->is_active ? 'Deactivate' : 'Activate' }}</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td class="p-4 text-center text-gray-500" colspan="6">No items yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->withQueryString()->links() }}</div>
</div>
@endsection
