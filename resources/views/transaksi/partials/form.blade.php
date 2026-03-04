<div class="mb-3">
    <label class="form-label">Rekening</label>
    <select name="rekening_id" class="form-select" required>
        <option value="">-- Pilih Rekening --</option>
        @foreach($rekenings as $rek)
        <option value="{{ $rek->id }}">
            {{ $rek->name_rek }}
            - Saldo: Rp {{ number_format($rek->saldo_akhir, 0, ',', '.') }}
        </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Departemen</label>
    <select name="departemen_id" class="form-select" required>
        <option value="">-- Pilih Departemen --</option>
        @foreach($departemensUser ?? [] as $dep)
        <option value="{{ $dep->id }}">
            {{ $dep->name_dep }}
        </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Program</label>
    <select name="program_id" class="form-select">
        <option value="">-- Pilih Program --</option>
        @foreach($programs as $prog)
        <option value="{{ $prog->id }}">
            {{ $prog->name_prog }}
        </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Kategori</label>

    <select class="kategoriSelect" name="kategori_id[]" multiple>
        @foreach($kategoris as $ktgr)
        <option value="{{ $ktgr->id }}">
            {{ $ktgr->name_ktgr }}
        </option>
        @endforeach
    </select>

    <small class="text-muted">
        Bisa cari dan pilih kategori satu per satu
    </small>
</div>

<div class="mb-3">
    <label class="form-label">Nominal</label>
    <input type="number" name="nominal_transaksi" class="form-control" min="1" required>
</div>

<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea name="keterangan" class="form-control" rows="3"></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Upload Bukti Nota</label>
    <input type="file" name="bukti_nota" class="form-control" accept="image/*,.pdf">
    <small class="text-muted">
        Format: JPG, PNG, PDF (Max 2MB)
    </small>
</div>