@extends('layouts.app')

@section('content')
<div class="container-xxl py-4">

  <h1 class="h4 mb-3">New Sale</h1>

  <form id="addForm" class="row g-2 mb-3">
    @csrf
    <div class="col-md-6">
      <select class="form-select" name="product_id" required>
        @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }} — Rp {{ number_format($p->price,0,',','.') }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <input type="number" step="0.001" min="0.001" class="form-control" name="qty" value="1" required>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100" type="submit">Add</button>
    </div>
  </form>

  <div class="card card-quiet rounded-4">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0" id="cartTable">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th class="text-end">Qty</th>
            <th class="text-end">Price</th>
            <th class="text-end">Line</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @php $sum=0; @endphp
          @foreach(($cart['lines'] ?? []) as $l)
            @php $line = $l['qty']*$l['price']; $sum += $line; @endphp
            <tr data-id="{{ $l['product_id'] }}">
              <td>{{ $l['name'] }}</td>
              <td class="text-end">{{ rtrim(rtrim(number_format($l['qty'],3,'.',''), '0'), '.') }}</td>
              <td class="text-end">Rp {{ number_format($l['price'],0,',','.') }}</td>
              <td class="text-end">Rp {{ number_format($line,0,',','.') }}</td>
              <td class="text-end">
                <button class="btn btn-outline-secondary btn-sm remove">Remove</button>
              </td>
            </tr>
          @endforeach
          @if(empty($cart['lines']))
            <tr><td colspan="5" class="text-center text-secondary py-4">Cart is empty</td></tr>
          @endif
        </tbody>
      </table>
    </div>

    <div class="card-body d-flex flex-wrap justify-content-end gap-3">
      <div class="fs-5"><strong>Total:</strong> Rp {{ number_format($sum,0,',','.') }}</div>

      <form action="{{ route('employee.sales.store') }}" method="post" class="d-flex gap-2">
        @csrf
        <select name="payment_method" class="form-select form-select-sm" style="max-width:140px">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="qris">QRIS</option>
        </select>
        <input type="number" step="0.01" name="paid" class="form-control form-control-sm" placeholder="Paid amount" required>
        <button class="btn btn-success">Checkout</button>
      </form>
    </div>
  </div>
</div>

<script>
const addForm = document.getElementById('addForm');
addForm?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const fd = new FormData(addForm);
  await fetch("{{ route('employee.sales.cart.add') }}", {
    method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: fd
  });
  location.reload();
});
document.querySelectorAll('.remove').forEach(btn=>{
  btn.addEventListener('click', async (e)=>{
    const id = e.target.closest('tr').dataset.id;
    const fd = new FormData(); fd.append('product_id', id);
    await fetch("{{ route('employee.sales.cart.remove') }}", {
      method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: fd
    });
    location.reload();
  });
});
</script>
@endsection
