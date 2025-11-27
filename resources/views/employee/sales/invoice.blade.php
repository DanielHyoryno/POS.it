@extends('layouts.app')

@section('content')
<div class="container-xxl py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Invoice {{ $sale->invoice_no }}</h1>
    <a class="btn btn-outline-secondary" onclick="window.print()">Print</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th>Product</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Price</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>

            <tbody class="table-group-divider">
              @foreach($sale->items as $it)
                <tr>
                  <td>{{ $it->product->name ?? '—' }}</td>
                  <td class="text-end">{{ $it->qty }}</td>
                  <td class="text-end">Rp {{ number_format($it->price,0,',','.') }}</td>
                  <td class="text-end">Rp {{ number_format($it->total,0,',','.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <span>Subtotal</span>
            <strong>Rp {{ number_format($sale->subtotal,0,',','.') }}</strong>
          </div>

          <div class="d-flex justify-content-between text-secondary">
            <span>Discount</span>
            <span>Rp {{ number_format($sale->discount,0,',','.') }}</span>
          </div>

          <div class="d-flex justify-content-between text-secondary">
            <span>Tax</span>
            <span>Rp {{ number_format($sale->tax,0,',','.') }}</span>
          </div>

          <hr>
          <div class="d-flex justify-content-between fs-5">
            <span>Total</span>
            <strong>Rp {{ number_format($sale->total,0,',','.') }}</strong>
          </div>

          <div class="d-flex justify-content-between mt-2">
            <span>Paid</span>
            <span>Rp {{ number_format($sale->paid,0,',','.') }}</span>
          </div>

          <div class="d-flex justify-content-between">
            <span>Change</span>
            <span>Rp {{ number_format($sale->change,0,',','.') }}</span>
          </div>
          
          <div class="mt-2">
            <span class="badge {{ $sale->status=='paid' ? 'bg-success-subtle text-success-emphasis':'bg-secondary-subtle text-secondary-emphasis' }}">
              {{ ucfirst($sale->status) }}
            </span>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">Payments</div>
        <ul class="list-group list-group-flush">
          @forelse($sale->payments as $p)
            <li class="list-group-item d-flex justify-content-between">
              <span>{{ strtoupper($p->method) }}</span>
              <strong>Rp {{ number_format($p->amount,0,',','.') }}</strong>
            </li>
          @empty
            <li class="list-group-item text-secondary">No payments.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
