@extends('layouts.main')

@section('content')
<main class="py-4">
    <div class="modal fade" id="customerFormModal" tabindex="-1" aria-labelledby="customerFormModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title h5" id="customerFormModalLabel">{{ $customer ? 'Edit Customer' : 'Add New Customer' }}</h1>
                    <a href="{{ route('customers.index') }}" class="btn-close" aria-label="Close"></a>
                </div>

                <form action="{{ $customer ? route('customers.update', $customer) : route('customers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($customer)
                    @method('PUT')
                    @endif
                    <div class="modal-body">
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                        @endif

                        @include('customers._fields', ['idPrefix' => 'customerPage', 'formCustomer' => $customer])
                        @if ($customer?->image_path)
                        <img src="{{ asset('storage/' . $customer->image_path) }}" alt="{{ $customer->name }}" class="mt-3 rounded" width="120" height="120" style="object-fit: cover;">
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">{{ $customer ? 'Update Customer' : 'Save Customer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerFormModal = document.getElementById('customerFormModal');
        if (customerFormModal && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(customerFormModal).show();
        }
    });
</script>
@endsection