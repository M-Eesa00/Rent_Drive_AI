@extends('layouts.main')

@section('content')
<main class="py-4 booking-wizard" id="bookingWizard">
    <div class="mx-auto" style="max-width: 900px;">
        <div class="mb-4">
            <a href="{{ route('cars.cars_list') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Back to vehicles
            </a>
            <h1 class="h2 fw-bold mb-1">Reserve your rental</h1>
            <p class="text-muted mb-0">Choose your trip, select an available vehicle, and send your request for admin approval.</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>We couldn’t continue your booking.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <ol class="booking-progress list-unstyled d-flex gap-2 mb-4" aria-label="Booking steps">
            <li class="booking-progress-step is-current" data-progress-step="1"><span>1</span> Trip</li>
            <li class="booking-progress-step" data-progress-step="2"><span>2</span> Vehicle &amp; details</li>
            <li class="booking-progress-step" data-progress-step="3"><span>3</span> Review</li>
        </ol>

        <form id="bookingWizardForm" action="{{ route('booking.submit') }}" method="POST" novalidate
            data-availability-url="{{ route('booking.availability') }}">
            @csrf
            <input type="hidden" name="car_id" id="selectedCarId" value="{{ old('car_id') }}">

            <section class="booking-step card border-0 shadow-sm" data-step="1" aria-labelledby="tripHeading">
                <div class="card-body p-4 p-md-5">
                    <p class="small text-primary text-uppercase fw-bold mb-2">Step 1 of 3</p>
                    <h2 class="h4 fw-bold" id="tripHeading">Where and when is your trip?</h2>
                    <p class="text-muted mb-4">Choose your pickup and return dates to find available vehicles.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label fw-semibold">Pick-up date</label>
                            <input type="date" class="form-control" id="startDate" name="start_date"
                                min="{{ now()->toDateString() }}" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label fw-semibold">Return date</label>
                            <input type="date" class="form-control" id="endDate" name="end_date"
                                min="{{ now()->addDay()->toDateString() }}" value="{{ old('end_date') }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-primary px-4" id="findVehiclesButton">
                            Find available vehicles <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div id="availabilityMessage" class="mt-3" role="status" aria-live="polite"></div>
                    <div id="vehicleResults" class="row g-3 mt-1" aria-live="polite"></div>
                </div>
            </section>

            <section class="booking-step card border-0 shadow-sm d-none" data-step="2" aria-labelledby="detailsHeading">
                <div class="card-body p-4 p-md-5">
                    <p class="small text-primary text-uppercase fw-bold mb-2">Step 2 of 3</p>
                    <h2 class="h4 fw-bold" id="detailsHeading">Choose a vehicle and add your details</h2>
                    <div id="selectedVehicleSummary" class="alert alert-primary mt-3 mb-4"></div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customerName" class="form-label fw-semibold">Full name</label>
                            <input type="text" class="form-control" id="customerName" name="customer_name"
                                value="{{ old('customer_name', $customer?->name) }}" autocomplete="name" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerEmail" class="form-label fw-semibold">Email address</label>
                            <input type="email" class="form-control" id="customerEmail" name="customer_email"
                                value="{{ old('customer_email', $customer?->email) }}" autocomplete="email" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label fw-semibold">Contact phone</label>
                            <input type="tel" class="form-control" id="customerPhone" name="customer_phone"
                                value="{{ old('customer_phone', $customer?->phone) }}" autocomplete="tel" maxlength="30" required>
                        </div>
                        <div class="col-12">
                            <label for="specialRequests" class="form-label fw-semibold">Special requests <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea class="form-control" id="specialRequests" name="special_requests" rows="3" maxlength="2000"
                                placeholder="For example: child seat request or accessibility needs">{{ old('special_requests') }}</textarea>
                            <div class="form-text">Requests are subject to availability and will be confirmed by our team.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-back="1">
                            <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Trip details
                        </button>
                        <button type="button" class="btn btn-primary px-4" data-next="3">
                            Review booking <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </section>

            <section class="booking-step card border-0 shadow-sm d-none" data-step="3" aria-labelledby="reviewHeading">
                <div class="card-body p-4 p-md-5">
                    <p class="small text-primary text-uppercase fw-bold mb-2">Step 3 of 3</p>
                    <h2 class="h4 fw-bold" id="reviewHeading">Review your booking</h2>
                    <p class="text-muted">Check the trip and contact details before submitting your booking request.</p>

                    <dl class="row gy-2 mt-3 mb-4 booking-review">
                        <dt class="col-sm-4">Vehicle</dt><dd class="col-sm-8" id="reviewVehicle"></dd>
                        <dt class="col-sm-4">Rental period</dt><dd class="col-sm-8" id="reviewDates"></dd>
                        <dt class="col-sm-4">Customer</dt><dd class="col-sm-8" id="reviewCustomer"></dd>
                        <dt class="col-sm-4">Email / phone</dt><dd class="col-sm-8" id="reviewContact"></dd>
                        <dt class="col-sm-4">Special requests</dt><dd class="col-sm-8" id="reviewRequests"></dd>
                        <dt class="col-sm-4">Total</dt><dd class="col-sm-8 fw-bold text-primary" id="reviewTotal"></dd>
                    </dl>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="booking_acknowledged" value="1" id="bookingTerms" required>
                        <label class="form-check-label" for="bookingTerms">I have reviewed these details and understand the booking is not confirmed until an admin approves it.</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-back="2">
                            <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i> Edit details
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="submitBookingButton">
                            Submit booking request <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </section>
        </form>

        <p class="small text-muted text-center mt-4">
            Your request will be saved immediately. The admin will review it, and registered customers receive an email after approval.
        </p>
    </div>
</main>
@endsection

@section('scripts')
<style>
    .booking-progress-step { display: flex; align-items: center; gap: .45rem; color: #6c757d; font-size: .9rem; flex: 1; }
    .booking-progress-step span { display: inline-grid; place-items: center; width: 1.8rem; height: 1.8rem; border: 1px solid #adb5bd; border-radius: 50%; }
    .booking-progress-step.is-current, .booking-progress-step.is-complete { color: #0d6efd; font-weight: 600; }
    .booking-progress-step.is-current span, .booking-progress-step.is-complete span { color: #fff; background: #0d6efd; border-color: #0d6efd; }
    .booking-review dt { color: #6c757d; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('bookingWizardForm');
        const results = document.getElementById('vehicleResults');
        const message = document.getElementById('availabilityMessage');
        const selectedCarId = document.getElementById('selectedCarId');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        let currentStep = 1;
        let selectedCar = null;
        let hasSearched = false;

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[character]);

        const showStep = (step) => {
            currentStep = step;
            document.querySelectorAll('.booking-step').forEach((section) => {
                section.classList.toggle('d-none', Number(section.dataset.step) !== step);
            });
            document.querySelectorAll('[data-progress-step]').forEach((item) => {
                const itemStep = Number(item.dataset.progressStep);
                item.classList.toggle('is-current', itemStep === step);
                item.classList.toggle('is-complete', itemStep < step);
            });
            document.getElementById('bookingWizard').scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const validateStep = (step, requireCar = true) => {
            const fields = [...document.querySelectorAll(`.booking-step[data-step="${step}"] [required]`)];
            for (const field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            if (step === 1 && requireCar && !selectedCar) {
                message.innerHTML = '<div class="alert alert-warning">Search and select an available vehicle first.</div>';
                return false;
            }
            return true;
        };

        const clearVehicleSearch = () => {
            const hadSearch = hasSearched || results.childElementCount > 0 || selectedCar !== null;
            hasSearched = false;
            selectedCar = null;
            selectedCarId.value = '';
            results.replaceChildren();
            document.getElementById('selectedVehicleSummary').textContent = '';
            if (hadSearch) {
                message.innerHTML = '<div class="alert alert-info">Your dates changed. Search again to see available vehicles for the new date range.</div>';
                if (currentStep > 1) showStep(1);
            }
        };

        document.querySelectorAll('[data-back]').forEach((button) => {
            button.addEventListener('click', () => showStep(Number(button.dataset.back)));
        });

        document.querySelectorAll('[data-next]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!validateStep(currentStep)) return;
                if (Number(button.dataset.next) === 3) updateReview();
                showStep(Number(button.dataset.next));
            });
        });

        const updateEndDateMin = () => {
            if (startDate.value) {
                const nextDay = new Date(`${startDate.value}T00:00:00`);
                nextDay.setDate(nextDay.getDate() + 1);
                endDate.min = nextDay.toISOString().slice(0, 10);
                if (endDate.value && endDate.value <= startDate.value) endDate.value = '';
            }
        };
        startDate.addEventListener('change', () => {
            updateEndDateMin();
            clearVehicleSearch();
        });
        endDate.addEventListener('change', clearVehicleSearch);

        document.getElementById('findVehiclesButton').addEventListener('click', async () => {
            message.textContent = '';
            results.replaceChildren();
            selectedCar = null;
            selectedCarId.value = '';
            document.getElementById('selectedVehicleSummary').textContent = '';
            if (!validateStep(1, false)) return;
            const button = document.getElementById('findVehiclesButton');
            button.disabled = true;
            message.innerHTML = '<div class="text-muted"><span class="spinner-border spinner-border-sm me-2" role="status"></span>Checking vehicles for your dates...</div>';
            const searchedStartDate = startDate.value;
            const searchedEndDate = endDate.value;
            try {
                const params = new URLSearchParams({ start_date: searchedStartDate, end_date: searchedEndDate });
                const response = await fetch(`${form.dataset.availabilityUrl}?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'Unable to search availability.');
                if (startDate.value !== searchedStartDate || endDate.value !== searchedEndDate) {
                    message.innerHTML = '<div class="alert alert-info">Your dates changed during the search. Search again to see current availability.</div>';
                    return;
                }
                hasSearched = true;
                if (!data.cars.length) {
                    message.innerHTML = '<div class="alert alert-warning">No vehicles are available for those dates. Try another date range.</div>';
                    selectedCar = null;
                    selectedCarId.value = '';
                    return;
                }
                message.innerHTML = `<p class="small text-success fw-semibold">${data.cars.length} vehicle(s) available for these dates:</p>`;
                results.innerHTML = data.cars.map((car) => `
                    <div class="col-md-6">
                        <label class="card h-100 booking-car-option">
                            ${car.image ? `<img src="${escapeHtml(car.image)}" class="card-img-top" alt="${escapeHtml(car.name)}" style="height:180px;object-fit:cover">` : ''}
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-2">
                                    <h3 class="h6 fw-bold">${escapeHtml(car.name)} <span class="text-muted">(${escapeHtml(car.year)})</span></h3>
                                    <input class="form-check-input car-choice" type="radio" name="car_choice" value="${escapeHtml(car.id)}" aria-label="Select ${escapeHtml(car.name)}">
                                </div>
                                <p class="small text-muted mb-2">${escapeHtml(car.seats)} seats · ${escapeHtml(car.gear_type)} · ${escapeHtml(car.fuel_type)}</p>
                                <p class="mb-0">Rs ${escapeHtml(car.daily_rate.toFixed(2))} per day <span class="text-muted">·</span> <strong>Rs ${escapeHtml(car.total_price.toFixed(2))} total</strong></p>
                            </div>
                        </label>
                    </div>`).join('');
                results.querySelectorAll('.car-choice').forEach((radio) => {
                    radio.addEventListener('change', () => {
                        selectedCar = data.cars.find((car) => String(car.id) === radio.value);
                        selectedCarId.value = radio.value;
                        document.getElementById('selectedVehicleSummary').textContent =
                            `${selectedCar.name} (${selectedCar.year}) — ${selectedCar.total_price.toFixed(2)} for the selected rental`;
                        message.innerHTML = '<div class="d-flex justify-content-end mt-3"><button type="button" class="btn btn-primary" id="continueToDetails">Continue with selected vehicle <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></button></div>';
                        document.getElementById('continueToDetails').addEventListener('click', () => showStep(2));
                    });
                });
                selectedCar = null;
                selectedCarId.value = '';
            } catch (error) {
                message.innerHTML = `<div class="alert alert-danger">${escapeHtml(error.message)}</div>`;
            } finally {
                button.disabled = false;
            }
        });

        function updateReview() {
            document.getElementById('reviewVehicle').textContent = `${selectedCar.name} (${selectedCar.year})`;
            document.getElementById('reviewDates').textContent = `${startDate.value} to ${endDate.value}`;
            document.getElementById('reviewCustomer').textContent = document.getElementById('customerName').value;
            document.getElementById('reviewContact').textContent =
                `${document.getElementById('customerEmail').value} · ${document.getElementById('customerPhone').value}`;
            document.getElementById('reviewRequests').textContent =
                document.getElementById('specialRequests').value || 'None';
            document.getElementById('reviewTotal').textContent = `Rs ${selectedCar.total_price.toFixed(2)}`;
        }

        form.addEventListener('submit', (event) => {
            if (!validateStep(3)) {
                event.preventDefault();
                return;
            }
            const button = document.getElementById('submitBookingButton');
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Submitting booking request...';
        });

        if (form.querySelector('.alert-danger')) {
            updateEndDateMin();
        }
    });
</script>
@endsection
