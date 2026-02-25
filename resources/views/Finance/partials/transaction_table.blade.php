<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Tanggal Transaksi</th>
                <th>Akun Keuangan</th>
                <th>Referensi</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d-m-Y') }}
                    </td>
                    <td>
                        @if ($trx->type == 'transfer')
                            <span class="badge bg-info bg-opacity-10 text-info">Transfer</span><br>
                            <small>{{ $trx->fromAccount->name ?? '-' }} <i class="iconoir-arrow-right"></i>
                                {{ $trx->toAccount->name ?? '-' }}</small>
                        @elseif($trx->type == 'income')
                            <span class="badge bg-success bg-opacity-10 text-success">Income</span><br>
                            <small>{{ $trx->toAccount->name ?? '-' }}</small>
                        @elseif($trx->type == 'expense')
                            <span class="badge bg-danger bg-opacity-10 text-danger">Expense</span><br>
                            <small>{{ $trx->fromAccount->name ?? '-' }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $trx->description }}
                        @if ($trx->reference_number)
                            <br><small class="text-muted">Ref:
                                {{ $trx->reference_number }}</small>
                        @endif
                    </td>
                    <td class="text-end">
                        @if (in_array($trx->type, ['income', 'transfer']))
                            @if ($trx->to_account_id)
                                Rp {{ number_format($trx->amount, 2) }}
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end">
                        @if (in_array($trx->type, ['expense', 'transfer']))
                            @if ($trx->from_account_id)
                                Rp {{ number_format($trx->amount, 2) }}
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($trx->status == 'posted')
                            <span class="badge bg-primary">Posted</span>
                        @elseif($trx->status == 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($trx->status == 'canceled')
                            <span class="badge bg-danger">Canceled</span>
                        @else
                            <span class="badge bg-light text-dark">{{ $trx->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Tindakan
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="iconoir-eye-alt me-2"></i> Detail</a>
                                </li>
                                @if ($trx->status == 'draft')
                                    <li><a class="dropdown-item text-success" href="#"><i class="iconoir-check me-2"></i> Post</a>
                                    </li>
                                @endif
                                @if ($trx->status != 'canceled')
                                    <li><a class="dropdown-item text-danger" href="#"><i class="iconoir-cancel me-2"></i> Cancel</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <img src="{{ asset('assets/images/no-data.svg') }}" alt="No Data" style="height: 100px; opacity: 0.5">
                        <p class="text-muted mt-3">Belum ada transaksi.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $transactions->withQueryString()->links() }}
</div>
