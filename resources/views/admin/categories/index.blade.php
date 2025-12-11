@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-4 ">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}"
            class="px-3 py-2 bg-indigo-600 text-white rounded">{{ __('+ New') }}</a>
    </div>

    @if (session('ok'))
        <div class="p-3 bg-green-50 border border-green-200 rounded dark:bg-green-900/30 dark:border-green-800 dark:text-green-300">{{ session('ok') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase border-b border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-500">
                <tr>
                    <th class="text-left p-3.5">{{ __('Name') }}</th>
                    <th class="p-3.5">{{ __('Active') }}</th>
                    <th class="p-3.5">{{ __('Order') }}</th>
                    <th class="p-3.5 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-600 dark:bg-gray-800 dark:text-gray-300">
                @forelse($cats as $c)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                    <td class="p-3.5">{{ $c->name }}</td>
                    <td class="p-3.5 text-center">
                        <span
                            class="px-2 py-0.5 rounded-full {{ $c->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                            {{ $c->is_active ? __('Yes') : __('No') }}
                        </span>
                    </td>

                    <td class="p-3.5 text-center">{{ $c->sort_order }}</td>

                    <td class="p-3.5 text-right">
                        <a href="{{ route('admin.categories.edit',$c) }}" class="px-2 py-1 text-xs border rounded">{{ __('Edit') }}</a>
                        
                        <form action="{{ route('admin.categories.destroy',$c) }}" method="POST" class="inline"
                            onsubmit="return confirm('{{ __('Delete category?') }}')">
                            @csrf @method('DELETE')
                            <button class="px-2 py-1 text-xs border rounded text-red-600 hover:bg-red-50 
                                           dark:text-red-400 dark:border-gray-600 dark:hover:bg-red-900/20 transition-colors">{{ __('Delete') }}</button>
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