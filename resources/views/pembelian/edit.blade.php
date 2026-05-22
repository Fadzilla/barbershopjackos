@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Edit Pembelian</h3>

    <form action="{{ route('pembelians.update', $pembelian->id) }}"
        method="POST">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label>No Faktur</label>

            <input type="text"
                name="no_faktur"
                class="form-control"
                value="{{ $pembelian->no_faktur }}">

        </div>

        <div class="mb-3">

            <label>Tanggal</label>

            <input type="date"
                name="tanggal"
                class="form-control"
                value="{{ $pembelian->tanggal }}">

        </div>

        <div class="mb-3">

            <label>Supplier</label>

            <input type="text"
                name="supplier"
                class="form-control"
                value="{{ $pembelian->supplier }}">

        </div>

        <button class="btn btn-primary">
            Update
        </button>

        <a href="{{ route('pembelians.index') }}"
            class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

@endsection