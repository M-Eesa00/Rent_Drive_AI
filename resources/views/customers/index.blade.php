@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="h2 mb-1">Customers</h1>
            <p class="text-muted mb-0">{{ $customers->count() }} customers</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
            <i class="fa-solid fa-user-plus me-1"></i> Add Customer
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
        <strong>Unable to save the customer.</strong>
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
                    <th>Phone</th>
                    <th>Address</th>
                    <th class="text-center">Bookings</th>
                    <th>Added</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                <tr>
                    <td class="text-nowrap">{{ $loop->iteration }}</td>
                    <td>
                        @if ($customer->image_path)
                        <img src="{{ asset('storage/' . $customer->image_path) }}" alt="{{ $customer->name }}" class="rounded" width="56" height="56" style="object-fit: cover;">
                        @else
                        <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted small" style="width: 56px; height: 56px;">No photo</div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address ?: 'Not provided' }}</td>
                    <td class="text-center">{{ $customer->bookings->count() }}</td>
                    <td>{{ $customer->created_at?->format('M j, Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer? Customers with booking history cannot be deleted.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">No customers have been added.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<div class="modal fade" id="addCustomerModal" data-open-on-error="{{ old('_form') === 'customer' ? 'true' : 'false' }}" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="addCustomerModalLabel">Add New Customer</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="customer">
                <div class="modal-body">@include('customers._fields', ['idPrefix' => 'modalCustomer', 'formCustomer' => null])</div>
                <div class="modal-footer">
                    <a href="{{ route('customers.create') }}" class="btn btn-link me-auto">Open full form</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
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
</script>
@endsection