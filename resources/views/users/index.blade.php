@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="h2 mb-1">Users</h1>
            <p class="text-muted mb-0">{{ $users->count() }} users</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa-solid fa-user-plus me-1"></i> Add User
        </button>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger mt-3" role="alert">
        <strong>Unable to save the user.</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col" class="text-nowrap">Sr #</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="text-nowrap">{{ $loop->iteration }}</td>
                    <td>
                        @if ($user->image_path)
                        <img src="{{ asset('storage/' . $user->image_path) }}" alt="{{ $user->name }}" class="rounded" width="56" height="56" style="object-fit: cover;">
                        @else
                        <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted small" style="width: 56px; height: 56px;">No photo</div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at?->format('M j, Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user account?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">No users have been added.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<div class="modal fade" id="addUserModal" data-open-on-error="{{ old('_form') === 'user' ? 'true' : 'false' }}" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="addUserModalLabel">Add New User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="user">
                <div class="modal-body">@include('users._fields', ['idPrefix' => 'modalUser', 'formUser' => null])</div>
                <div class="modal-footer">
                    <a href="{{ route('users.create') }}" class="btn btn-link me-auto">Open full form</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalWithErrors = document.querySelector('[data-open-on-error="true"]');
        if (modalWithErrors && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalWithErrors).show();
        }
    });

    //ALERTS TIMEOUT 
    document.querySelectorAll('.alert').forEach((alertElement) => {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertElement).close();
        }, 3000);
    });
</script>
@endsection