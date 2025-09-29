@if ($errors->any())
  <div class="p-3 mb-4 bg-red-100 text-red-800 rounded">
    <ul class="list-disc pl-6">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif
