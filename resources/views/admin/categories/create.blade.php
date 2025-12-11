@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-8 px-4">
    <div class="shadow-xl rounded-lg border overflow-hidden
        bg-gray-900 border-gray-700 
        dark:bg-gray-200 dark:border-gray-300">
        
        <div class="p-6 space-y-6">
            
            <h1 class="text-2xl font-bold border-b pb-4 mb-4 text-gray-800 border-gray-700 
                dark:text-gray-200 dark:border-gray-300">
                {{ __('New Category') }}
            </h1>

            @if ($errors->any())
                <div class="p-3 rounded-lg text-sm border
                    bg-red-600 text-white border-red-500
                    dark:bg-red-200 dark:text-red-900 dark:border-red-300">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1
                        text-gray-700
                        dark:text-gray-200">
                        {{ __('Name') }}
                    </label>
                    
                    <input name="name" class="w-full rounded-lg shadow-sm p-2 border transition duration-200 focus:ring focus:ring-opacity-50
                        bg-gray-200 border-gray-600 text-dark focus:border-indigo-500 focus:ring-indigo-500
                        dark:bg-white dark:border-gray-400 dark:text-gray-900 dark:placeholder-gray-500" 
                        placeholder="{{ __('Name') }}" 
                        value="{{ old('name') }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 mt-3
                        text-gray-700
                        dark:text-gray-200">
                        {{ __('Sort Order') }}
                    </label>
                    <input name="sort_order" type="number" class="w-full rounded-lg shadow-sm p-2 border transition duration-200 focus:ring focus:ring-opacity-50
                        bg-gray-200 border-gray-600 text-dark focus:border-indigo-500 focus:ring-indigo-500
                        dark:bg-white dark:border-gray-400 dark:text-gray-900 dark:placeholder-gray-500"
                        placeholder="{{ __('Sort order (0..)') }}">
                </div>

                <div class="flex items-center mt-3 mb-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="rounded shadow-sm focus:ring focus:ring-opacity-50 
                        border-gray-600 text-indigo-500 bg-gray-800 
                        dark:bg-white dark:border-gray-400 dark:text-indigo-600">
                    
                    <label for="is_active" class="ml-2 block text-sm cursor-pointer
                        text-gray-800
                        dark:text-gray-200">
                        {{ __('Active') }}
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-6 pt-4 border-t
                    border-gray-700
                    dark:border-gray-300">
                    
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm font-medium transition duration-200
                        bg-gray-700 text-gray-800 hover:bg-gray-600
                        dark:bg-white dark:text-gray-200 ">
                        {{ __('Cancel') }}
                    </a>
                    
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium shadow-md transition duration-200">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection