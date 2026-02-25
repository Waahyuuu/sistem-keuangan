@extends('layouts.app')

@php
$titles = [
'rekening' => 'Rekening',
'kantor' => 'Kantor',
'departemen' => 'Unit/Departemen',
'program' => 'Program',
'kategori' => 'Kategori',
];

$currentTab = $titles[$tab] ?? null;
@endphp

@section('title', $currentTab ? 'Master Data Setup | ' . $currentTab : 'Master Data Setup')

@section('content')

<h3 class="mb-4">Master Data Setup</h3>

<div class="card p-3 mb-4">
    <div class="d-flex gap-3">

        <a href="{{ route('master.data', ['tab' => 'rekening']) }}"
            class="btn {{ $tab == 'rekening' ? 'btn-primary' : 'btn-light' }}">
            Rekening
        </a>

        <a href="{{ route('master.data', ['tab' => 'kantor']) }}"
            class="btn {{ $tab == 'kantor' ? 'btn-primary' : 'btn-light' }}">
            Kantor
        </a>

        <a href="{{ route('master.data', ['tab' => 'departemen']) }}"
            class="btn {{ $tab == 'departemen' ? 'btn-primary' : 'btn-light' }}">
            Unit/Departemen
        </a>

        <a href="{{ route('master.data', ['tab' => 'program']) }}"
            class="btn {{ $tab == 'program' ? 'btn-primary' : 'btn-light' }}">
            Program
        </a>

        <a href="{{ route('master.data', ['tab' => 'kategori']) }}"
            class="btn {{ $tab == 'kategori' ? 'btn-primary' : 'btn-light' }}">
            Kategori
        </a>

    </div>
</div>

{{-- Konten tab --}}
<div class="card p-4">

    @includeIf('master-data.partials.' . $tab)

</div>

@endsection