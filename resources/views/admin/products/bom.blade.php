@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-4">
  <h1 class="text-2xl font-bold">Edit BOM — {{ $product->name }}</h1>

  @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded">
      <ul class="list-disc pl-5 text-sm">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  @if (session('ok')) 
    <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div> 
  @endif

  @if (session('error'))
    <div class="p-3 bg-red-50 border border-red-200 rounded">{{ session('error') }}</div> 
  @endif

  <form method="POST" action="{{ route('admin.products.bom.update', $product) }}" class="space-y-3">
    @csrf @method('PUT')

    <div id="bom-rows" class="space-y-2">
      @php $rows = old('bom.item_id', $product->bomLines->pluck('item_id')->toArray()); @endphp
      @php $qtys = old('bom.qty',     $product->bomLines->pluck('qty')->toArray()); @endphp

      @if(empty($rows))
        @php $rows = ['']; $qtys = ['']; @endphp
      @endif

      @foreach($rows as $i => $iid)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div>
            <select name="bom[item_id][]" class="w-full border p-2 rounded" required>
              <option value="">— select item —</option>
              @foreach($items as $it)
                <option value="{{ $it->id }}" @selected($iid==$it->id)>{{ $it->name }} ({{ $it->base_unit }})</option>
              @endforeach
            </select>
          </div>

          <div>
            <input name="bom[qty][]" type="number" step="0.001" min="0.001"
                   value="{{ is_array($qtys)?($qtys[$i]??''):$qtys }}"
                   class="w-full border p-2 rounded" placeholder="Qty" required>
          </div>
          
          <div class="flex items-center">
            <button type="button" class="px-3 py-2 border rounded remove-row">Remove</button>
          </div>

        </div>
      @endforeach
    </div>

    <div>
      <button type="button" id="add-row" class="px-3 py-2 border rounded">+ Add Row</button>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
      <div>Estimated Cost (current): Rp {{ number_format($estimatedCost,2,',','.') }}</div>
      <div class="text-sm text-gray-600">*Cost uses each Item's cost_price.</div>
    </div>

    <div class="text-right">
      <a href="{{ route('admin.products.show',$product) }}" class="px-4 py-2 border rounded">Back</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save BOM</button>
    </div>
    
  </form>
</div>

<script>
  document.getElementById('add-row').addEventListener('click', () => {
    const container = document.getElementById('bom-rows');
    const tpl = `
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-2">
      <div>
        <select name="bom[item_id][]" class="w-full border p-2 rounded" required>
          <option value="">— select item —</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}">{{ $it->name }} ({{ $it->base_unit }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <input name="bom[qty][]" type="number" step="0.001" min="0.001" class="w-full border p-2 rounded" placeholder="Qty" required>
      </div>
      <div class="flex items-center">
        <button type="button" class="px-3 py-2 border rounded remove-row">Remove</button>
      </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', tpl);
  });

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-row')) {
      e.target.closest('.grid').remove();
    }
  });
</script>
@endsection
