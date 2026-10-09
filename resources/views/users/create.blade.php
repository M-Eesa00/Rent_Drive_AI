@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2 mb-0">{{ $user ? 'Edit User' : 'Add New User' }}</h1>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back to Users</a>
            </div>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ $user ? route('users.update', $user) : route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($user)
                @method('PUT')
                @endif
                @include('users._fields', ['idPrefix' => 'userPage', 'formUser' => $user])
                @if ($user?->image_path)
                <img src="{{ asset('storage/' . $user->image_path) }}" alt="{{ $user->name }}" class="mt-3 rounded" width="120" height="120" style="object-fit: cover;">
                @endif
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">{{ $user ? 'Update User' : 'Save User' }}</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection