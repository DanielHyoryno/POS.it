@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-4">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('Categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}"
            class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('+ New') }}</a>
    </div>

    @if (session('ok'))
    <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase tracking-wide">
                <tr>
                    <th class="text-left p-3.5">{{ __('Name') }}</th>
                    <th class="p-3.5">{{ __('Active') }}</th>
                    <th class="p-3.5">{{ __('Order') }}</th>
                    <th class="p-3.5 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($cats as $c)
                <tr>
                    <td class="p-3.5">{{ $c->name }}</td>
                    <td class="p-3.5 text-center">
                        <span
                            class="px-2 py-0.5 rounded-full {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                            {{ $c->is_active ? __('Yes') : __('No') }}
                        </span>
                    </td>

                    <td class="p-3.5 text-center">{{ $c->sort_order }}</td>

                    <td class="p-3.5 text-right">
                        <a href="{{ route('admin.categories.edit',$c) }}"
                            class="px-2 py-1 text-xs border rounded">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.categories.destroy',$c) }}" method="POST" class="inline"
                            onsubmit="return confirm('{{ __('Delete category?') }}')">
                            @csrf @method('DELETE')
                            <button class="px-2 py-1 text-xs border rounded">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">{{ __('No categories yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $cats->links() }}</div>
</div>
@endsection