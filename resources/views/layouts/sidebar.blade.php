<div class="bg-light p-3" style="width:250px; min-height:100vh;">
    <h4 class="text-primary">Keuangan</h4>
    <hr>

    <a href="{{ route('dashboard') }}" class="d-block mb-2">Dashboard</a>
    <a href="{{ route('transaksi.index') }}" class="d-block mb-2">Transaksi</a>
    <a href="{{ route('master.data') }}" class="d-block mb-2">Master Data</a>
    <a href="{{ route('laporan') }}" class="d-block mb-2">Laporan</a>
    <a href="{{ route('pengaturan') }}" class="d-block mb-2">Pengaturan Akun</a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-danger btn-sm mt-3">Logout</button>
    </form>
</div>