<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Name" class="form-label">Full name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="{{ $idPrefix }}Name" name="name" value="{{ old('name', $formCustomer?->name) }}" maxlength="255" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Email" class="form-label">Email address <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="{{ $idPrefix }}Email" name="email" value="{{ old('email', $formCustomer?->email) }}" maxlength="255" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Phone" class="form-label">Phone number <span class="text-danger">*</span></label>
        <input type="tel" class="form-control" id="{{ $idPrefix }}Phone" name="phone" value="{{ old('phone', $formCustomer?->phone) }}" maxlength="17" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Image" class="form-label">Customer photo</label>
        <input type="file" class="form-control" id="{{ $idPrefix }}Image" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
        <div class="form-text">Optional, maximum size 5 MB.</div>
    </div>
    <div class="col-12">
        <label for="{{ $idPrefix }}Address" class="form-label">Address</label>
        <textarea class="form-control" id="{{ $idPrefix }}Address" name="address" rows="3" maxlength="500">{{ old('address', $formCustomer?->address) }}</textarea>
    </div>
</div>