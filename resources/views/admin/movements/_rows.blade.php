@foreach ($rows as $mv)
  @php
    $qty = (float) $mv->change_qty;
    $unit = $mv->item->base_unit ?? '';
    $signClass = $qty < 0 ? 'text-danger' : ($qty > 0 ? 'text-success' : 'text-muted');
  @endphp

  <div class="list-group-item d-flex align-items-start justify-content-between">
    <div class="me-3">
      <div class="fw-medium">{{ $mv->item->name ?? '—' }}</div>
      <div class="small text-secondary">
        {{ $mv->created_at->format('Y-m-d H:i') }} —
        {{ ucfirst($mv->reason ?? '-') }}
        @if(!empty($mv->note)) — <span class="text-muted">{{ $mv->note }}</span>@endif
      </div>
    </div>
    
    <div class="text-end">
      <div class="fw-semibold {{ $signClass }}">
        {{ rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.') }}
      </div>
      <div class="small text-secondary">{{ $unit }}</div>
    </div>
  </div>
@endforeach
