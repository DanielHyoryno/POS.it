@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">New Item</h1>

  @include('partials.errors')

  <form method="POST" action="{{ route('admin.items.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block mb-1 font-medium">Name</label>
      <input name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Base Unit</label>
      <select name="base_unit" class="w-full border p-2 rounded" required>
        <option value="g"  @selected(old('base_unit')==='g')>g (grams)</option>
        <option value="ml" @selected(old('base_unit')==='ml')>ml (milliliters)</option>
        <option value="pcs"@selected(old('base_unit')==='pcs')>pcs (pieces)</option>
      </select>
      <p class="text-sm text-gray-500 mt-1">All stock will be stored in this unit. Conversions (kg→g, L→ml) are handled on restock.</p>
    </div>

    <div>
      <label class="block mb-1 font-medium">Low Stock Threshold</label>
      <input name="low_stock_threshold" type="number" step="0.001" min="0" value="{{ old('low_stock_threshold', 0) }}" class="w-full border p-2 rounded" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Cost Price (optional)</label>
      <input name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price') }}" class="w-full border p-2 rounded">
    </div>

    <div>
      <label class="block mb-1 font-medium">Image (optional)</label>
      <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
      @isset($item->image_path)
        <img src="{{ Storage::url($item->image_path) }}" class="mt-2 h-20 rounded object-cover">
      @endisset
    </div>


    <div class="flex items-center space-x-2">
      <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
      <label for="is_active">Active</label>
    </div>

    <div class="flex items-center justify-end space-x-2">
      <a href="{{ route('admin.items.index') }}" class="px-4 py-2 border rounded">Cancel</a>
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    </div>
  </form>
</div>
@endsection
