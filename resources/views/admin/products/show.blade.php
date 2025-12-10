@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-4">
    @if (session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div>
    @endif

    @if (session('error'))
    <div class="p-3 bg-red-50 border border-red-200 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-start justify-between">
        <div class="flex gap-4">
            @if($product->image_path)
            <img src="{{ Storage::url($product->image_path) }}" class="h-20 w-20 rounded object-cover"
                alt="{{ $product->name }}">
            @else
            <div class="h-20 w-20 rounded bg-gray-200 grid place-items-center text-gray-500 text-xs">IMG</div>
            @endif

            <div>
                <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                @if($product->sku)
                <div class="text-gray-600">{{ __('SKU') }}: {{ $product->sku }}</div>
                @endif

                <div class="text-gray-600 capitalize">{{ __('Type') }}: {{ $product->type }}</div>

                @if($product->category)
                <div class="text-gray-600">{{ __('Category') }}: {{ $product->category->name }}</div>
                @endif

                <div>{{ __('Price') }}: <b>Rp {{ number_format($product->selling_price,2,',','.') }}</b></div>

                <div class="mt-2">
                    Status:
                    <span
                        class="px-2 py-0.5 rounded {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                        {{ $product->is_active ? __('Active') : __('Inactive') }}
                    </span>
                </div>

            </div>
        </div>

        <div class="space-x-2">
            @if($product->isComposite())
            <a href="{{ route('admin.products.bom.edit', $product) }}"
                class="px-3 py-2 border rounded">{{ __('Edit BOM') }}</a>
            @endif
            <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-2 border rounded">{{ __('Edit') }}</a>
            <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button
                    class="px-3 py-2 border rounded">{{ $product->is_active ? __('Deactivate') : __('Activate') }}</button>
            </form>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
        <div class="font-medium">
            {{ __('Estimated Cost: Rp :cost', ['cost' => number_format($estimatedCost,2,',','.')]) }}</div>
        <div>
            {{ __('Margin (est.): Rp :margin', ['margin' => number_format($product->selling_price - $estimatedCost,2,',','.')]) }}
        </div>
        @if($product->selling_price < $estimatedCost) <div class="text-red-600 mt-1">
            {{ __('Warning: Selling price is below estimated cost.') }}
    </div>
    @endif
</div>

@if($product->isSimple() && $product->linkedItem)
<div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">{{ __('Linked Item') }}</h2>
    <div>{{ $product->linkedItem->name }} — {{ __('per sale qty') }}:
        <b>{{ rtrim(rtrim(number_format($product->per_sale_qty,3,'.',''), '0'), '.') }}
            {{ $product->linkedItem->base_unit }}</b>
    </div>
</div>
@endif

@if($product->isComposite())
<div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">{{ __('BOM Lines') }}</h2>
    <table class="min-w-full">

        <thead class="bg-gray-50">
            <tr>
                <th class="text-left p-2">{{ __('Item') }}</th>
                <th class="text-right p-2">{{ __('Qty') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse($product->bomLines as $line)
            <tr class="border-t">
                <td class="p-2">{{ $line->item->name }} ({{ $line->item->base_unit }})</td>
                <td class="p-2 text-right">{{ rtrim(rtrim(number_format($line->qty,3,'.',''), '0'), '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="p-3 text-center text-gray-500">{{ __('No BOM lines.') }}</td>
            </tr>
            @endforelse
        </tbody>

    </table>
</div>
@endif
</div>
@endsection