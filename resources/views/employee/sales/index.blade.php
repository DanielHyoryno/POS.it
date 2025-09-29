@extends('layouts.app')
@section('content')
<div class="container-xxl py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Products</h1>
    <div>
      <a class="btn btn-outline-secondary me-2" href="{{ route('employee.sales.cart.show') }}">Cart</a>
      <a class="btn btn-primary" href="{{ route('employee.sales.checkout.show') }}">Checkout</a>
    </div>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input name="q" class="form-control" placeholder="Search products..." value="{{ request('q') }}">
      <button class="btn btn-outline-secondary">Search</button>
    </div>
  </form>

  <div class="row g-3">
    @foreach($products as $p)
      @php
        $available = false;
        if ($p->is_active) {
          if ($p->type === 'simple' && $p->item) {
            $available = $p->item->current_qty > 0;
          } elseif ($p->type === 'composite' && $p->bomComponents->isNotEmpty()) {
            $available = $p->bomComponents->every(fn($i)=> $i->current_qty >= ($i->pivot->qty_per ?: 0.001));
          }
        }
      @endphp

      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100">
          <div class="card-body d-flex flex-column">
            <div class="small text-secondary text-uppercase">{{ ucfirst($p->type) }}</div>
            <h6 class="mb-1">{{ $p->name }}</h6>
            <div class="mb-2 fw-semibold">Rp {{ number_format($p->price ?? 0,0,',','.') }}</div>
            <span class="badge {{ $available ? 'bg-success-subtle text-success-emphasis':'bg-danger-subtle text-danger-emphasis' }}">
              {{ $available ? 'Available' : 'Not available' }}
            </span>
            <form class="mt-auto pt-2" action="{{ route('employee.sales.cart.add') }}" method="post">
              @csrf
              <input type="hidden" name="product_id" value="{{ $p->id }}">
              <div class="input-group">
                <input name="qty" type="number" step="0.001" min="0.001" class="form-control" value="1" {{ $available ? '' : 'disabled' }}>
                <button class="btn btn-primary" {{ $available ? '' : 'disabled' }}>Add</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
