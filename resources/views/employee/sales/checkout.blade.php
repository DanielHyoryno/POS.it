@extends('layouts.app')
@section('content')
<div class="container-xxl py-4">
  <h1 class="h5 mb-3">Checkout</h1>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr>
            </thead>
            <tbody class="table-group-divider">
              @foreach($cart['lines'] as $l)
                <tr>
                  <td>{{ $l['name'] }}</td>
                  <td class="text-end">{{ $l['qty'] }}</td>
                  <td class="text-end">Rp {{ number_format($l['price'],0,',','.') }}</td>
                  <td class="text-end">Rp {{ number_format(($l['price']*$l['qty'])-($l['discount']??0),0,',','.') }}</td>
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
          <div class="d-flex justify-content-between"><span>Subtotal</span><strong>Rp {{ number_format($totals['subtotal'],0,',','.') }}</strong></div>
          <div class="d-flex justify-content-between text-secondary"><span>Discount</span><span>Rp {{ number_format($totals['discount'],0,',','.') }}</span></div>
          <div class="d-flex justify-content-between text-secondary"><span>Tax</span><span>Rp {{ number_format($totals['tax'],0,',','.') }}</span></div>
          <hr>
          <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>Rp {{ number_format($totals['total'],0,',','.') }}</strong></div>

          <form class="mt-3" action="{{ route('employee.sales.checkout.store') }}" method="post">
            @csrf
            <div class="mb-2">
              <label class="form-label">Payment Method</label>
              <select name="method" class="form-select">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="qris">QRIS</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Paid Amount</label>
              <input type="number" step="0.01" min="0" name="paid" class="form-control" required>
            </div>
            <button class="btn btn-success w-100">Confirm & Pay</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
