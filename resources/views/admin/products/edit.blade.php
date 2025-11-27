@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-4">
  <h1 class="text-2xl font-bold">Edit Product</h1>

  @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded">
      <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $e) 
          <li>{{ $e }}</li> 
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('ok')) 
    <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div> 
  @endif

  @if (session('error')) 
    <div class="p-3 bg-red-50 border border-red-200 rounded">{{ session('error') }}</div> 
  @endif

  <form method="POST" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data" class="space-y-4">
    @csrf 
    @method('PUT')

    <div>
      <label class="block mb-1 font-medium">Name</label>
      <input name="name" value="{{ old('name',$product->name) }}" class="w-full border p-2 rounded" required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block mb-1 font-medium">SKU (optional)</label>
        <input name="sku" value="{{ old('sku',$product->sku) }}" class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block mb-1 font-medium">Type</label>
        <select name="type" class="w-full border p-2 rounded" required>
          <option value="simple" @selected(old('type',$product->type)==='simple')>Simple</option>
          <option value="composite" @selected(old('type',$product->type)==='composite')>Composite</option>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Selling Price</label>
        <input name="selling_price" type="number" step="0.01" min="0" value="{{ old('selling_price',$product->selling_price) }}" class="w-full border p-2 rounded" required>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1 font-medium">Category</label>
        <select name="category_id" class="w-full border p-2 rounded">
          <option value="">— none —</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id',$product->category_id)==$cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">Image (optional)</label>
        <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
        @if($product->image_path)
          <img src="{{ Storage::url($product->image_path) }}" class="mt-2 h-20 w-20 rounded object-cover" alt="{{ $product->name }}">
        @endif
      </div>
    </div>

    @if($product->isSimple())
      <div class="space-y-2">
        <div class="text-sm text-gray-600">Linked Item & per sale qty (base unit)</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <select name="linked_item_id" class="w-full border p-2 rounded">
              <option value="">— select item —</option>
              @foreach($items as $it)
                <option value="{{ $it->id }}" @selected(old('linked_item_id',$product->linked_item_id)==$it->id)>
                  {{ $it->name }} ({{ $it->base_unit }}) {{ $it->is_active ? '' : '— inactive' }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <input name="per_sale_qty" type="number" step="0.001" min="0.001" value="{{ old('per_sale_qty',$product->per_sale_qty) }}" class="w-full border p-2 rounded">
          </div>

        </div>
      </div>
    @endif

    {{-- Cost awareness --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
      <div class="font-medium">Estimated Cost: Rp {{ number_format($estimatedCost,2,',','.') }}</div>
      <div>Margin (est.): Rp {{ number_format($product->selling_price - $estimatedCost,2,',','.') }}</div>
      @if($product->selling_price < $estimatedCost)
        <div class="text-red-600 mt-1">Warning: Selling price is below estimated cost.</div>
      @endif
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$product->is_active))> 
      <span>Active</span>
    </div>

    <div class="text-right">
      <a href="{{ route('admin.products.show',$product) }}" class="px-4 py-2 border rounded">Back</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
    </div>
  </form>

  @if($product->isComposite())
  <div class="bg-white rounded shadow p-4">
    <div class="flex items-center justify-between mb-2">
      <h2 class="font-semibold">BOM Lines (quick view)</h2>
      <a href="{{ route('admin.products.bom.edit', $product) }}" class="px-3 py-2 border rounded">Edit BOM</a>
    </div>

    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left p-2">Item</th>
          <th class="text-right p-2">Qty</th>
        </tr>
      </thead>

      <tbody>
        @forelse($product->bomLines as $line)
          <tr class="border-t">
            <td class="p-2">{{ $line->item->name }} ({{ $line->item->base_unit }})</td>
            <td class="p-2 text-right">{{ rtrim(rtrim(number_format($line->qty,3,'.',''), '0'), '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="2" class="p-3 text-center text-gray-500">No BOM lines.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
