@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 space-y-4">
    <h1 class="text-2xl font-bold dark:text-white">{{ __('New Category') }}</h1>

    @if ($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded"></div>
    <ul class="list-disc pl-5 text-sm">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
        @csrf
        <input name="name" class="border p-2 rounded w-full" placeholder="{{ __('Name') }}" value="{{ old('name') }}"
            required>
        <input name="sort_order" type="number" class="border p-2 rounded w-full"
            placeholder="{{ __('Sort order (0..)') }}">
        <label class="inline-flex items-center gap-2 dark:text-white">
            <input type="checkbox" name="is_active" value="1" checked>
            <span>{{ __('Active') }}</span>
        </label>

        <div class="text-right">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border rounded dark:text-white">{{ __('Cancel') }}</a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection