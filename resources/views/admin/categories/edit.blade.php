@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-8 px-4">
    <div class="bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 shadow-xl rounded-xl border border-gray-700 dark:border-gray-300 overflow-hidden">
        
        <div class="p-6 space-y-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white dark:border-gray-300 border-b pb-4 mb-4">
                {{ __('Edit Category') }}
            </h1>

            @if (session('ok'))
                <div class="p-3 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm dark:bg-green-200 dark:text-green-900 dark:border-green-300">
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-50 text-red-700 border border-red-200 rounded-lg dark:bg-red-200 dark:text-red-900 dark:border-red-300">
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
                @csrf 
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">{{ __('Name') }}</label>
                    <input name="name" class="w-full rounded-lg shadow-sm p-2 border transition duration-200
                        bg-gray-200 border-gray-600 text-dark focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50
                        dark:bg-white dark:border-gray-300 dark:text-gray-900" 
                        placeholder="{{ __('Category Name') }}"
                        value="{{ old('name', $category->name) }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">{{ __('Sort Order') }}</label>
                    <input name="sort_order" type="number" class="w-full rounded-lg shadow-sm p-2 border transition duration-200
                        bg-gray-200 border-gray-600 text-dark focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50
                        dark:bg-white dark:border-gray-300 dark:text-gray-900" 
                        placeholder="{{ __('Ex: 1') }}"
                        value="{{ old('sort_order', $category->sort_order) }}">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @checked(old('is_active', $category->is_active))>
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300 cursor-pointer">
                        {{ __('Active Status') }}
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition duration-200
                        bg-gray-700 text-gray-800 hover:bg-gray-600
                        dark:bg-gray-300 dark:text-gray-400 dark:hover:bg-gray-400">
                        {{ __('Cancel') }}
                    </a>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-md transition duration-200">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection