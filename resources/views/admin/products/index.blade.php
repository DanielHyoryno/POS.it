@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">+ New Product</a>
  </div>

  <form method="GET" class="mb-3 flex flex-wrap gap-2">
    <input name="search" value="{{ request('search') }}" class="border p-2 rounded" placeholder="Search name/SKU">
    <select name="type" class="border p-2 rounded">
      <option value="">All Types</option>
      <option value="simple" @selected(request('type')==='simple')>Simple</option>
      <option value="composite" @selected(request('type')==='composite')>Composite</option>
    </select>
    <select name="status" class="border p-2 rounded">
      <option value="">Any Status</option>
      <option value="active" @selected(request('status')==='active')>Active</option>
      <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
    </select>
    <button class="px-3 py-2 border rounded">Filter</button>
  </form>

  @if (session('ok')) <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div> @endif
  @if (session('error')) <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded">{{ session('error') }}</div> @endif

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left p-3">Product</th>
          <th class="text-left p-3">Type / Category</th>
          <th class="text-right p-3">Price</th>
          <th class="text-center p-3">Status</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
      @forelse($products as $p)
        <tr class="border-t">
          <td class="p-3">
            <div class="flex items-center gap-3">
              @if($p->image_path)
                <img src="{{ Storage::url($p->image_path) }}" class="h-10 w-10 rounded object-cover" alt="{{ $p->name }}">
              @else
                <div class="h-10 w-10 rounded bg-gray-200 grid place-items-center text-gray-500 text-xs">IMG</div>
              @endif
              <div>
                <a class="text-blue-600 hover:underline font-medium" href="{{ route('admin.products.show',$p) }}">{{ $p->name }}</a>
                @if($p->sku) <div class="text-xs text-gray-500">SKU: {{ $p->sku }}</div> @endif
              </div>
            </div>
          </td>
          <td class="p-3">
            <div class="capitalize">{{ $p->type }}</div>
            @if($p->category) <div class="text-xs text-gray-500">{{ $p->category->name }}</div> @endif
          </td>
          <td class="p-3 text-right">Rp {{ number_format($p->selling_price,2,',','.') }}</td>
          <td class="p-3 text-center">
            <span class="px-2 py-0.5 rounded {{ $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
              {{ $p->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="p-3 text-right space-x-2">
            @if($p->isComposite())
              <a href="{{ route('admin.products.bom.edit', $p) }}" class="px-2 py-1 text-sm border rounded">BOM</a>
            @endif
            <a href="{{ route('admin.products.edit', $p) }}" class="px-2 py-1 text-sm border rounded">Edit</a>
            <form action="{{ route('admin.products.toggle', $p) }}" method="POST" class="inline">
              @csrf @method('PATCH')
              <button class="px-2 py-1 text-sm border rounded">{{ $p->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="5">No products yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection
