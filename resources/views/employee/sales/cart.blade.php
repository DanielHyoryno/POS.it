@extends('layouts.app')
@section('content')
<div class="container-xxl py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Cart</h1>
    <form action="{{ route('employee.sales.cart.clear') }}" method="post" onsubmit="return confirm('Clear cart?')">
      @csrf @method('DELETE')
      <button class="btn btn-outline-danger btn-sm">Clear</button>
    </form>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th class="text-end" style="width:160px">Qty</th>
            <th class="text-end">Price</th>
            <th class="text-end">Line</th>
            <th class="text-end" style="width:120px"></th>
          </tr>
        </thead>
        <tbody>
          @php $lines = $cart['lines'] ?? []; @endphp
          @forelse($lines as $l)
            @php $line = ($l['price'] * $l['qty']) - ($l['discount'] ?? 0); @endphp
            <tr>
              <td>{{ $l['name'] }}</td>

              <td class="text-end">
                {{-- per-row update form (no nesting issues) --}}
                <form action="{{ route('employee.sales.cart.update') }}" method="post" class="d-flex gap-2 justify-content-end">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $l['product_id'] }}">
                  <input class="form-control form-control-sm text-end" style="max-width:90px"
                         type="number" step="0.001" min="0" name="qty" value="{{ $l['qty'] }}">
                  <button class="btn btn-sm btn-outline-primary">Update</button>
                </form>
              </td>

              <td class="text-end">Rp {{ number_format($l['price'], 0, ',', '.') }}</td>
              <td class="text-end">Rp {{ number_format($line, 0, ',', '.') }}</td>

              <td class="text-end">
                <form action="{{ route('employee.sales.cart.remove') }}" method="post" onsubmit="return confirm('Remove this item?')">
                  @csrf
                  <input type="hidden" name="product_id" value="{{ $l['product_id'] }}">
                  <button class="btn btn-outline-secondary btn-sm">Remove</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-secondary py-4">Cart is empty</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body d-flex justify-content-end gap-3">
      <div class="fs-6">
        <strong>Total:</strong>
        Rp {{ number_format($totals['total'] ?? 0, 0, ',', '.') }}
      </div>
      <a class="btn btn-primary" href="{{ route('employee.sales.checkout.show') }}">Checkout</a>
    </div>
  </div>
</div>
@endsection
