@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 space-y-4">
  <h1 class="text-2xl font-bold">New Category</h1>

  @if ($errors->any()) 
    <div class="p-3 bg-red-50 border border-red-200 rounded"></div>
    <ul class="list-disc pl-5 text-sm">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach</ul>
  @endif

  <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
    @csrf
    <input name="name" class="border p-2 rounded w-full" placeholder="Name" value="{{ old('name') }}" required>
    <input name="sort_order" type="number" class="border p-2 rounded w-full" placeholder="Sort order (0..)">
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" checked>
      <span>Active</span>
    </label>
    
    <div class="text-right">
      <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border rounded">Cancel</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
    </div>
  </form>
</div>
@endsection
