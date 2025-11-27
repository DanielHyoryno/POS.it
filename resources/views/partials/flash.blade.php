@if (session('ok'))
  <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('ok') }}</div>
@endif

@if (session('error'))
  <div class="p-3 mb-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
@endif
