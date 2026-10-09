<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Name" class="form-label">Full name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="{{ $idPrefix }}Name" name="name" value="{{ old('name', $formUser?->name) }}" maxlength="255" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Email" class="form-label">Email address <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="{{ $idPrefix }}Email" name="email" value="{{ old('email', $formUser?->email) }}" maxlength="255" required>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}Password" class="form-label">Password @unless($formUser)<span class="text-danger">*</span>@else<span class="text-muted">(leave blank to keep current)</span>@endunless</label>
        <input type="password" class="form-control" id="{{ $idPrefix }}Password" name="password" minlength="6" autocomplete="new-password" {{ $formUser ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label for="{{ $idPrefix }}PasswordConfirmation" class="form-label">Confirm password @unless($formUser)<span class="text-danger">*</span>@endunless</label>
        <input type="password" class="form-control" id="{{ $idPrefix }}PasswordConfirmation" name="password_confirmation" minlength="6" autocomplete="new-password" {{ $formUser ? '' : 'required' }}>
    </div>
    <div class="col-12">
        <label for="{{ $idPrefix }}Image" class="form-label">Profile photo</label>
        <input type="file" class="form-control" id="{{ $idPrefix }}Image" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
        <div class="form-text">Optional, maximum size 5 MB.</div>
    </div>
</div>