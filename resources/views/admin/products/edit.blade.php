@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('content')

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl card-soft p-6 space-y-4">
    @csrf @method('PUT')
    @include('admin.products._form')
    <button class="btn-primary">Perbarui Produk</button>
</form>
@endsection
