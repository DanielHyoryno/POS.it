@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-4">
    <h1 class="text-2xl font-bold">{{ __('New Product') }}</h1>

    @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded">
        <ul class="list-disc pl-5 text-sm">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-medium">{{ __('Name') }}</label>
            <input name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1 font-medium">{{ __('SKU (optional)') }}</label>
                <input name="sku" value="{{ old('sku') }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block mb-1 font-medium">{{ __('Type') }}</label>
                <select name="type" class="w-full border p-2 rounded" required>
                    <option value="simple" @selected(old('type')==='simple' )>{{ __('Simple') }}</option>
                    <option value="composite" @selected(old('type')==='composite' )>{{ __('Composite') }}</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">{{ __('Selling Price') }}</label>
                <input name="selling_price" type="number" step="0.01" min="0" value="{{ old('selling_price',0) }}"
                    class="w-full border p-2 rounded" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium">{{ __('Category') }}</label>
                <select name="category_id" class="w-full border p-2 rounded">
                    <option value="">{{ __('none') }}</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">{{ __('Image (optional)') }}</label>
                <input type="file" name="image" accept="image/*" class="w-full border p-2 rounded">
            </div>
        </div>

        {{-- Simple linkage --}}
        <div class="space-y-2">
            <div class="text-sm text-gray-600">{{ __('For simple products:') }}</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">{{ __('Linked Item') }}</label>
                    <select name="linked_item_id" class="w-full border p-2 rounded">
                        <option value="">— {{ __('select item') }} —</option>
                        @foreach($items as $it)
                        <option value="{{ $it->id }}" @selected(old('linked_item_id')==$it->id)>
                            {{ $it->name }} ({{ $it->base_unit }}) {{ $it->is_active ? '' : __('inactive') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">{{ __('Per Sale Qty (base unit)') }}</label>
                    <input name="per_sale_qty" type="number" step="0.001" min="0.001" value="{{ old('per_sale_qty') }}"
                        class="w-full border p-2 rounded">
                </div>

            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
            <span>{{ __('Active') }}</span>
        </div>

        <div class="text-right">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border rounded">{{ __('Cancel') }}</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection