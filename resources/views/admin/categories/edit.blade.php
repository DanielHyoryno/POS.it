@extends('layouts.app')
@section('content')
<div class="max-w-md mx-auto p-6 space-y-4">
  <h1 class="text-2xl font-bold">Edit Category</h1>
  @if (session('ok')) <div class="p-3 bg-green-50 border border-green-200 rounded">{{ session('ok') }}</div> @endif
  @if ($errors->any()) <div class="p-3 bg-red-50 border border-red-200 rounded">
    <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>@endif
  <form action="{{ route('admin.categories.update',$category) }}" method="POST" class="space-y-3">
    @csrf @method('PUT')
    <input name="name" class="border p-2 rounded w-full" value="{{ old('name',$category->name) }}" required>
    <input name="sort_order" type="number" class="border p-2 rounded w-full" value="{{ old('sort_order',$category->sort_order) }}">
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$category->is_active))> <span>Active</span></label>
    <div class="text-right">
      <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border rounded">Back</a>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
    </div>
  </form>
</div>
@endsection
