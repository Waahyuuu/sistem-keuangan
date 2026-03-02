<div class="container-fluid">

    <div class="mb-3 text-center">
        <h5 class="fw-bold">
            {{ ucfirst($transaksi->type_transaksi) }}
        </h5>

        <h4 class="
            fw-bold
            @if($transaksi->type_transaksi === 'pemasukan') text-success
            @elseif($transaksi->type_transaksi === 'pengeluaran') text-danger
            @elseif($transaksi->type_transaksi === 'transfer') text-warning
            @else text-primary
            @endif
        ">
            Rp {{ number_format($transaksi->nominal_transaksi, 0, ',', '.') }}
        </h4>
    </div>

    <hr>

    <div class="row mb-2">
        <div class="col-5 fw-semibold">Rekening</div>
        <div class="col-7">
            {{ $transaksi->rekening->name_rek ?? '-' }}
        </div>
    </div>

    @if($transaksi->type_transaksi === 'transfer')
    <div class="row mb-2">
        <div class="col-5 fw-semibold">Rekening Tujuan</div>
        <div class="col-7">
            {{ optional($transaksi->rekeningTujuan)->name_rek ?? '-' }}
        </div>
    </div>
    @endif

    <div class="row mb-2">
        <div class="col-5 fw-semibold">Departemen</div>
        <div class="col-7">
            {{ $transaksi->departemen->name_dep ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-5 fw-semibold">Program</div>
        <div class="col-7">
            {{ $transaksi->program->name_prog ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-5 fw-semibold">Kategori</div>
        <div class="col-7">
            {{ $transaksi->kategori->name_ktgr ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-5 fw-semibold">Keterangan</div>
        <div class="col-7">
            {{ $transaksi->keterangan ?? '-' }}
        </div>
    </div>

    <div class="row">
        <div class="col-5 fw-semibold">Tanggal</div>
        <div class="col-7">
            {{ $transaksi->tgl_transaksi->format('d M Y') }}
        </div>
    </div>

</div>