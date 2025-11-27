@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Edit Item</h1>

  @include('partials.errors')
  @include('partials.flash')

  <form method="POST" action="{{ route('admin.items.update', $item) }}" class="space-y-4">
    @csrf 
    @method('PUT')

    <div>
      <label class="block mb-1 font-medium">Name</label>
      <input name="name" value="{{ old('name', $item->name) }}" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Base Unit</label>
      <input value="{{ $item->base_unit }}" class="w-full border p-2 rounded bg-gray-100" disabled>
      <p class="text-sm text-gray-500 mt-1">Base unit is immutable after creation.</p>
    </div>

    <div>
      <label class="block mb-1 font-medium">Low Stock Threshold</label>
      <input name="low_stock_threshold" type="number" step="0.001" min="0" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Cost Price (optional)</label>
      <input name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', $item->cost_price) }}" class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="block mb-1 font-medium">Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
      @isset($item->image_path)
        <img src="{{ Storage::url($item->image_path) }}" class="mt-2 h-20 rounded object-cover">
      @endisset
    </div>

    <div class="flex items-center space-x-2">
      <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
      <label for="is_active">Active</label>
    </div>

    <div class="flex items-center justify-end space-x-2">
      <a href="{{ route('admin.items.show', $item) }}" class="px-4 py-2 border rounded">Back</a>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
    </div>
  </form>
</div>
@endsection
