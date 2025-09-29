@foreach($movements as $mv)
  <div class="px-4 py-3 flex items-center justify-between border-b">
    <div>
      <div class="font-medium">{{ $mv->item->name ?? '—' }}</div>
      <div class="text-sm text-gray-500">
        {{ $mv->created_at->format('Y-m-d H:i') }} • {{ ucfirst($mv->reason) }}
        @if($mv->note) — {{ $mv->note }} @endif
      </div>
    </div>
    <div class="font-mono {{ $mv->change_qty < 0 ? 'text-red-600' : 'text-green-700' }}">
      {{ $mv->change_qty > 0 ? '+' : '' }}
      {{ rtrim(rtrim(number_format($mv->change_qty,3,'.',''), '0'), '.') }}
      {{ $mv->item->base_unit ?? '' }}
    </div>
  </div>
@endforeach
