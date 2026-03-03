@php use Illuminate\Support\Str; @endphp

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
            @if($transaksi->kategoris->count())
            @foreach($transaksi->kategoris as $kategori)
            @php
            $bg = $kategori->color_ktgr ?? '#6c757d';
            $hex = str_replace('#', '', $bg);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            $textColor = $brightness > 155 ? '#000' : '#fff';
            @endphp

            <span class="badge me-1 mb-1" style="background-color: {{ $bg }}; color: {{ $textColor }};">
                {{ $kategori->name_ktgr }}
            </span>
            @endforeach
            @else
            -
            @endif
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

    {{-- ================== BUKTI NOTA ================== --}}
    @if($transaksi->bukti_nota)
    <hr>
    <div class="row">
        <div class="col-12 text-center">
            <label class="fw-semibold">Bukti Nota</label><br>

            @if(Str::endsWith($transaksi->bukti_nota, '.pdf'))
            <a href="{{ asset('storage/'.$transaksi->bukti_nota) }}" target="_blank"
                class="btn btn-outline-primary mt-2">
                Lihat PDF
            </a>
            @else
            <img src="{{ asset('storage/'.$transaksi->bukti_nota) }}" class="img-fluid rounded shadow mt-2"
                style="max-height:300px; cursor:pointer;"
                onclick="openImagePreview('{{ asset('storage/'.$transaksi->bukti_nota) }}')">
            @endif

        </div>
    </div>
    @endif

</div>

{{-- ================== FULLSCREEN IMAGE VIEWER ================== --}}
<div id="imagePreviewOverlay" class="preview-overlay">
    <div class="preview-wrapper">

        <!-- Tombol Close -->
        <span onclick="closeImagePreview()" class="preview-close">
            &times;
        </span>

        <!-- Gambar -->
        <img id="previewImage" class="preview-image">
    </div>
</div>