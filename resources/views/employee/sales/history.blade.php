@extends('layouts.app')

@section('content')
<div class="container-xxl py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Sales History</h1>

    <form class="d-flex gap-2" action="{{ route('employee.sales.history.index') }}" method="get">
      <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" />
      <button class="btn btn-sm btn-primary">Go</button>
    </form>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.sales.history.index', ['date'=>$prevDate]) }}">← {{ \Illuminate\Support\Carbon::parse($prevDate)->format('M j, Y') }}</a>
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('employee.sales.history.index', ['date'=>$nextDate]) }}">{{ \Illuminate\Support\Carbon::parse($nextDate)->format('M j, Y') }} →</a>
    </div>

    <div class="d-flex gap-3">
      <div><span class="text-secondary">Orders:</span> <strong>{{ number_format($orderCount) }}</strong></div>
      <div><span class="text-secondary">Revenue:</span> <strong>Rp {{ number_format($totalRevenue,0,',','.') }}</strong></div>
      <div><span class="text-secondary">Avg:</span> <strong>Rp {{ number_format($avgOrder,0,',','.') }}</strong></div>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Time</th>
            <th>Invoice</th>
            <th>Cashier</th>
            <th class="text-end">Total</th>
            <th class="text-center">Status</th>
            <th class="text-end"></th>
          </tr>
        </thead>
        <tbody class="table-group-divider">
          @forelse($sales as $s)
            <tr>
              <td>{{ \Illuminate\Support\Carbon::parse($s->created_at)->format('H:i') }}</td>
              <td class="font-monospace">{{ $s->invoice_no }}</td>
              <td>{{ $s->user->name ?? '—' }}</td>
              <td class="text-end">Rp {{ number_format($s->total,0,',','.') }}</td>
              <td class="text-center">
                @php
                  $badge = match($s->status){
                    'paid' => 'bg-success-subtle text-success-emphasis',
                    'draft'=> 'bg-warning-subtle text-warning-emphasis',
                    'void' => 'bg-secondary-subtle text-secondary-emphasis',
                    default=> 'bg-secondary-subtle text-secondary-emphasis'
                  };
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($s->status) }}</span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('employee.sales.invoice.show', $s) }}">Invoice</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-secondary py-4">No sales on {{ \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
