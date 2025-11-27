@extends('layouts.app')

@section('content')
<style>
/* light gradient backdrop inside the page only */
.dash-backdrop {
    background: radial-gradient(80% 50% at 50% 0%, rgba(99, 102, 241, .08), rgba(14, 165, 233, .06), rgba(16, 185, 129, .05) 70%, transparent);
}

.card-quiet {
    border: 1px solid rgba(15, 23, 42, .08);
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
}

.icon-chip {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #f8f9fa;
    border: 1px solid rgba(0, 0, 0, .06);
}

.table-sticky thead th {
    position: sticky;
    top: 0;
    background: #f8f9fa;
    z-index: 2;
}
</style>

<div class="dash-backdrop">
    <div class="container-xxl py-4 py-md-5">

        {{-- Header Bar --}}
        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h1 class="h3 h2-md fw-bold mb-1 text-dark">{{ __('Admin Dashboard') }}</h1>
                <div class="text-secondary">{{ __('Welcome back') }}, {{ auth()->user()->name }} 👑</div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.items.index') }}"
                    class="btn btn-primary rounded-3">{{ __('Manage Items') }}</a>
                <a href="{{ route('admin.products.index') }}"
                    class="btn btn-indigo rounded-3 btn-secondary">{{ __('Manage Products') }}</a>
            </div>
        </div>

        {{-- KPI Cards --}}
        @php
        $cards = [
        ['label'=>__('Total Items'),'value'=>$totalItems,'icon'=>'box'],
        ['label'=>__('Active Items'),'value'=>$activeItems,'icon'=>'bolt'],
        ['label'=>__('Low Stock'),'value'=>$lowStockCount,'danger'=> (bool) $lowStockCount,'icon'=>'warn'],
        ['label'=>__('Total Products'),'value'=>$totalProducts,'icon'=>'layers'],
        ['label'=>__('Active Products'),'value'=>$activeProducts,'icon'=>'check'],
        ['label'=>__('Simple / Composite'),'value'=>"$simpleCount / $compositeCount",'small'=>true,'icon'=>'grid'],
        ];
        @endphp


        <section class="card card-quiet rounded-4 overflow-hidden my-4">
            <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-2">
                <strong>{{ __('Sales Overview') }}</strong>
                <div class="btn-group" role="group" aria-label="Range">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-range="1d">{{ __('1d') }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary active"
                        data-range="1m">{{ __('1m') }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-range="1w">{{ __('1w') }}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-range="1y">{{ __('1y') }}</button>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-8">
                        <canvas id="salesChart" height="120"></canvas>
                    </div>

                    <div class="col-12 col-md-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-secondary">{{ __('Revenue') }}</span>
                                <strong id="sumRevenue">Rp 0</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-secondary">{{ __('Expenses') }}</span>
                                <strong id="sumExpenses">Rp 0</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-secondary">{{ __('Profit') }}</span>
                                <strong id="sumProfit">Rp 0</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-secondary">{{ __('Orders') }}</span>
                                <strong id="sumOrders">0</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-secondary">{{ __('Avg Order Value') }}</span>
                                <strong id="avgOrder">Rp 0</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3 g-md-4 mb-4">
            @foreach ($cards as $c)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                <div class="card card-quiet rounded-4 h-100">
                    <div class="card-body d-flex gap-3 align-items-start">
                        <div
                            class="icon-chip {{ ($c['danger'] ?? false) ? 'bg-danger-subtle border-danger-subtle' : '' }}">
                            @switch($c['icon'])
                            @case('box')
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.75 7.5 12 12l8.25-4.5M12 21V12" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.75 7.5 12 3l8.25 4.5v9L12 21l-8.25-4.5v-9Z" />
                            </svg>
                            @break
                            @case('bolt')
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z" />
                            </svg>
                            @break
                            @case('warn')
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-danger">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="m12 3 9 16H3L12 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v4m0 4h.01" />
                            </svg>
                            @break
                            @case('layers')
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="m12 3 8 4-8 4-8-4 8-4Zm0 14 8-4m-8 4-8-4m8 8 8-4m-8 4-8-4" />
                            </svg>
                            @break
                            @case('check')
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            @break
                            @default
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                class="text-secondary">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                            @endswitch
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-uppercase small text-secondary">{{ $c['label'] }}</div>
                            <div
                                class="fw-semibold mt-1 {{ ($c['small'] ?? false) ? 'fs-5' : 'fs-2' }} {{ ($c['danger'] ?? false) ? 'text-danger' : 'text-dark' }}">
                                {{ $c['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Three Columns --}}
        <div class="row g-3 g-md-4">

            {{-- BOM Health --}}
            <div class="col-12 col-lg-4">
                <section class="card card-quiet rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-body-tertiary"><strong>{{ __('BOM Health') }}</strong></div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-secondary">{{ __('Composite without BOM') }}</span>
                            <span
                                class="fw-semibold {{ $compositeWithoutBom ? 'text-danger' : 'text-dark' }}">{{ $compositeWithoutBom }}</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-secondary">{{ __('Simple with inactive Item') }}</span>
                            <span
                                class="fw-semibold {{ $simpleWithInactiveItem ? 'text-warning' : 'text-dark' }}">{{ $simpleWithInactiveItem }}</span>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-secondary">{{ __('Products missing cost data') }}</span>
                            <span
                                class="fw-semibold {{ $productsNeedingCostData ? 'text-warning' : 'text-dark' }}">{{ $productsNeedingCostData }}</span>
                        </div>
                    </div>

                    <div class="card-footer bg-body-tertiary text-end">
                        <a href="{{ route('admin.products.index') }}"
                            class="link-primary">{{ __('Go to Products') }}</a>
                    </div>
                </section>
            </div>

            {{-- Low Stock Items --}}
            <div class="col-12 col-lg-4">
                <section class="card card-quiet rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between">
                        <div>
                            <strong>{{ __('Low Stock Items') }}</strong>
                            <div class="small text-secondary">{{ __('Below threshold based on base unit.') }}</div>
                        </div>
                        <a href="{{ route('admin.items.index',['low'=>1]) }}"
                            class="link-primary">{{ __('View all') }}</a>
                    </div>

                    <div class="table-responsive" style="max-height: 420px;">
                        <table class="table table-sm align-middle table-sticky mb-0">
                            <thead>
                                <tr class="text-secondary text-uppercase small">
                                    <th scope="col">{{ __('Item') }}</th>
                                    <th scope="col" class="text-end">{{ __('Stock') }}</th>
                                    <th scope="col" class="text-end">{{ __('Threshold') }}</th>
                                    <th scope="col" class="text-end"></th>
                                </tr>
                            </thead>

                            <tbody class="table-group-divider">
                                @forelse($lowStockItems as $it)
                                <tr class="">
                                    <td>
                                        <a class="link-primary"
                                            href="{{ route('admin.items.show',$it->id) }}">{{ $it->name }}</a>
                                    </td>
                                    <td class="text-end">
                                        {{ rtrim(rtrim(number_format($it->current_qty,3,'.',''), '0'), '.') }}
                                        {{ $it->base_unit }}
                                    </td>
                                    <td class="text-end">
                                        {{ rtrim(rtrim(number_format($it->low_stock_threshold,3,'.',''), '0'), '.') }}
                                        {{ $it->base_unit }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.items.show',$it->id) }}"
                                            class="btn btn-outline-secondary btn-sm rounded-3">{{ __('Restock') }}</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">{{ __('No low stock') }} 🎉
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- Recent Movements --}}
            <div class="col-12 col-lg-4">
                <section class="card card-quiet rounded-4 h-100 overflow-hidden d-flex flex-column">
                    <div class="card-header bg-body-tertiary">
                        <strong>{{ __('Recent Movements') }}</strong>
                        <div class="small text-secondary">{{ __('Latest first. Click to load more.') }}</div>
                    </div>

                    <div id="mv-list" class="list-group list-group-flush flex-grow-1"
                        style="max-height: 520px; overflow:auto;"></div>

                    <div class="d-flex justify-content-between align-items-center p-2 border-top bg-body-tertiary">
                        <div id="mv-loading" class="text-secondary small d-none">{{ __('Loading…') }}</div>
                        <div class="d-flex gap-2">
                            <button id="mv-more" class="btn btn-outline-secondary btn-sm">{{ __('Load more') }}</button>
                            <span id="mv-done" class="text-muted small d-none">{{ __('No more records') }} 🎉</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- Near Expiry Lots --}}
        <section class="card card-quiet rounded-4 overflow-hidden my-4">
            <div class="card-header bg-body-tertiary"><strong>{{ __('Expiring Soon (≤7d)') }}</strong></div>
            <div class="list-group list-group-flush">
                @forelse($nearExpiryLots as $lot)
                @php
                $daysLeft = now()->diffInDays($lot->expiry_date,false);
                $toneKey = $daysLeft <= 2 ? 'danger' : ($daysLeft <=4 ? 'warn' : 'caution' ); $border=['danger'=>
                    'border-danger-subtle','warn'=>'border-warning-subtle','caution'=>'border-warning-subtle'][$toneKey];
                    $badge = ['danger'=>'bg-danger-subtle text-danger-emphasis','warn'=>'bg-warning-subtle
                    text-warning-emphasis','caution'=>'bg-warning-subtle text-warning-emphasis'][$toneKey];
                    @endphp

                    <div
                        class="list-group-item d-flex justify-content-between align-items-center border-start border-4 {{ $border }}">
                        <div>
                            <div class="fw-medium">{{ $lot->item->name }}</div>
                            <div class="small text-secondary">
                                {{ $lot->expiry_date->format('Y-m-d') }}
                                <span class="badge rounded-pill {{ $badge }} ms-2">{{ $daysLeft }}d left</span>
                            </div>
                        </div>

                        <div class="font-monospace">
                            {{ rtrim(rtrim(number_format($lot->qty,3,'.',''), '0'), '.') }} {{ $lot->item->base_unit }}
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-secondary py-5">{{ __('No near-expiry lots 🎉') }}
                    </div>
                    @endforelse
            </div>
        </section>

        {{-- Products Snapshot --}}
        <section class="card card-quiet rounded-4 overflow-hidden">
            <div class="card-header bg-body-tertiary d-flex align-items-center justify-content-between">
                <strong>{{ __('Products Snapshot') }}</strong>
                <a href="{{ route('admin.products.index') }}" class="link-primary">{{ __('View all') }}</a>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small">
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th class="text-end">{{ __('Price') }}</th>
                            <th class="text-end">{{ __('Est. Cost') }}</th>
                            <th class="text-end">{{ __('Margin') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody class="table-group-divider">
                        @forelse($sample as $p)
                        <tr>
                            <td>
                                <a class="link-primary"
                                    href="{{ route('admin.products.show',$p['id']) }}">{{ $p['name'] }}</a>
                                @if($p['needs_bom'])
                                <span class="badge bg-danger-subtle text-danger-emphasis ms-2">{{ __('No BOM') }}</span>
                                @endif
                                @if($p['simple_item_inactive'])
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis ms-2">{{ __('Item Inactive') }}</span>
                                @endif
                                @if($p['missing_cost'])
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis ms-2">{{ __('Missing Cost') }}</span>
                                @endif
                            </td>

                            <td class="text-capitalize">{{ $p['type'] }}</td>
                            <td class="text-end">Rp {{ number_format($p['price'],2,',','.') }}</td>
                            <td class="text-end">Rp {{ number_format($p['cost'],2,',','.') }}</td>
                            <td class="text-end {{ $p['margin'] < 0 ? 'text-danger' : '' }}">Rp
                                {{ number_format($p['margin'],2,',','.') }}</td>

                            <td class="text-center">
                                @if($p['is_active'])
                                <span class="badge bg-success-subtle text-success-emphasis">{{ __('Active') }}</span>
                                @else
                                <span
                                    class="badge bg-secondary-subtle text-secondary-emphasis">{{ __('Inactive') }}</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a class="btn btn-outline-secondary btn-sm rounded-3"
                                    href="{{ route('admin.products.edit',$p['id']) }}">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">{{ __('No products yet.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const chartLabels = {
    revenue: @json(__('Revenue')),
    expenses: @json(__('Expenses')),
    profit: @json(__('Profit')),
    orders: @json(__('Orders'))
};
</script>

<script>
(function() {
    const routeBase = "{{ route('admin.analytics.sales') }}";
    const fmtIDR = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

    const ctx = document.getElementById('salesChart').getContext('2d');
    let chart;

    function render(data) {
        const {
            labels,
            revenue,
            orders,
            expenses,
            profit
        } = data;

        const sumRev = revenue.reduce((a, b) => a + b, 0);
        const sumExp = expenses.reduce((a, b) => a + b, 0);
        const sumPro = profit.reduce((a, b) => a + b, 0);
        const sumOrd = orders.reduce((a, b) => a + b, 0);
        const avgOrd = sumOrd ? sumRev / sumOrd : 0;

        document.getElementById('sumRevenue').textContent = fmtIDR(sumRev);
        document.getElementById('sumExpenses').textContent = fmtIDR(sumExp);
        document.getElementById('sumProfit').textContent = fmtIDR(sumPro);
        document.getElementById('sumOrders').textContent = sumOrd.toString();
        document.getElementById('avgOrder').textContent = fmtIDR(avgOrd);

        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                        label: chartLabels.revenue,
                        data: revenue,
                        tension: .35,
                        borderWidth: 2,
                        pointRadius: 0,
                        borderColor: 'green',
                        yAxisID: 'y'
                    },
                    {
                        label: chartLabels.expenses,
                        data: expenses,
                        tension: .35,
                        borderWidth: 2,
                        pointRadius: 0,
                        borderColor: 'red',
                        yAxisID: 'y'
                    },
                    {
                        label: chartLabels.profit,
                        data: profit,
                        tension: .35,
                        borderWidth: 2,
                        pointRadius: 0,
                        borderColor: 'blue',
                        yAxisID: 'y'
                    },
                    {
                        label: chartLabels.orders,
                        data: orders,
                        tension: .35,
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointRadius: 0,
                        borderColor: 'gray',
                        yAxisID: 'y1'
                    },
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        position: 'left',
                        ticks: {
                            callback: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                        }
                    },
                    y1: {
                        position: 'right',
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    }

    async function load(range = '1m') {
        const res = await fetch(`${routeBase}?range=${range}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        render(data);
    }

    document.querySelectorAll('[data-range]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-range]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            load(btn.dataset.range);
        });
    });

    load('1m');
})();
</script>


<script>
(function() {
    const list = document.getElementById('mv-list');
    const loading = document.getElementById('mv-loading');
    const done = document.getElementById('mv-done');
    const moreBtn = document.getElementById('mv-more');

    let nextUrl = @json(route('admin.movements.feed', ['per' => 6]));
    let busy = false;

    async function fetchPage() {
        if (busy || !nextUrl) return;
        busy = true;
        loading.classList.remove('d-none');
        moreBtn.disabled = true;

        try {
            const res = await fetch(nextUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.html) list.insertAdjacentHTML('beforeend', data.html);
            nextUrl = data.next_url;
            if (!nextUrl) {
                moreBtn.classList.add('d-none');
                done.classList.remove('d-none');
            }
        } catch (e) {
            console.error(e);
            moreBtn.classList.add('d-none');
        } finally {
            loading.classList.add('d-none');
            moreBtn.disabled = false;
            busy = false;
        }
    }

    moreBtn.addEventListener('click', fetchPage);

    fetchPage();
})();
</script>
@endsection