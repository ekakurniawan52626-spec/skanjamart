@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('content')

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl card-soft p-6 space-y-4">
    @csrf
    @include('admin.products._form')
    <button class="btn-primary">Simpan Produk</button>
</form>
@endsection
