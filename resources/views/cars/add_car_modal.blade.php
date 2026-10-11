<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCarModalLabel">Add a new Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="car">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="carBrand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="carBrand" name="brand" required>
                        </div>
                        <div class="col-md-6">
                            <label for="carModel" class="form-label">Model</label>
                            <input type="text" class="form-control" id="carModel" name="model" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carYear" class="form-label">Year</label>
                            <input type="number" class="form-control" id="carYear" name="year" min="2000" max="2030" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carPlateNumber" class="form-label">Plate Number</label>
                            <input type="text" class="form-control" id="carPlateNumber" name="plate_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carMileage" class="form-label">Mileage (km)</label>
                            <input type="number" class="form-control" id="carMileage" name="totalMileage" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label for="carDailyRate" class="form-label">Daily Rate</label>
                            <input type="number" class="form-control" id="carDailyRate" name="daily_rate" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <span class="form-label d-block">Gear Type</span>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="carGearAutomatic" name="gear_type" value="Automatic" required>
                                <label class="form-check-label" for="carGearAutomatic">Automatic</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="carGearManual" name="gear_type" value="Manual" required>
                                <label class="form-check-label" for="carGearManual">Manual</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="carFuelType" class="form-label">Fuel Type</label>
                            <select class="form-select" id="carFuelType" name="fuel_type" required>
                                <option value="" selected disabled>Choose...</option>
                                <option value="Gasoline">Gasoline</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Petrol">Petrol</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="carSeats" class="form-label">Number of Seats</label>
                            <input type="number" class="form-control" id="carSeats" name="seats" min="1" max="15" required>
                        </div>
                        <div class="col-md-6">
                            <label for="carStatus" class="form-label">Status</label>
                            <select class="form-select" id="carStatus" name="status" required>
                                <option value="available" selected>Available</option>
                                <option value="rented">Rented</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="carImage" class="form-label">Car Image</label>
                            <input type="file" class="form-control" id="carImage" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
                            <div class="form-text">Maximum size: 5 MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Car</button>
                </div>
            </form>
        </div>
    </div>
</div>