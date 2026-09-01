@extends('layouts.app')

@section('title', 'Create Account - ReproCare')

@push('scripts')
<script>
    // Prevent browser autofill
    window.addEventListener('load', function() {
        setTimeout(function() {
            const fields = ['first_name', 'middle_initial', 'last_name', 'email', 'password', 'password_confirmation', 'purok_id'];
            fields.forEach(function(id) {
                const el = document.getElementById(id);
                if (el && el.value && !el.dataset.userEntered) el.value = '';
            });
        }, 100);
    });

    // Sequential ID capture (front then back)
    let stream = null;
    let currentStep = 'front'; // 'front' or 'back'
    let frontImageData = null;
    let backImageData = null;

    const startCameraBtn = document.getElementById('start-camera-btn');
    const captureBtn = document.getElementById('capture-btn');
    const retakeBtn = document.getElementById('retake-btn');
    const uploadBtn = document.getElementById('upload-btn');
    const idImageUpload = document.getElementById('id-image-upload');
    const cameraFeed = document.getElementById('camera-feed');
    const capturedImage = document.getElementById('captured-image');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const idCaptureTitle = document.getElementById('id-capture-title');
    const idImageDataFront = document.getElementById('id_image_data_front');
    const idImageDataBack = document.getElementById('id_image_data_back');

    // Start camera
    startCameraBtn.addEventListener('click', async function() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } 
            });
            cameraFeed.srcObject = stream;
            cameraFeed.style.display = 'block';
            capturedImage.style.display = 'none';
            cameraPlaceholder.style.display = 'none';
            startCameraBtn.style.display = 'none';
            captureBtn.style.display = 'inline-block';
            uploadBtn.style.display = 'none';
        } catch (err) {
            alert('Unable to access camera. Please check permissions or use the upload option instead.');
            console.error('Camera error:', err);
        }
    });

    // Capture photo
    captureBtn.addEventListener('click', function() {
        const canvas = document.createElement('canvas');
        canvas.width = cameraFeed.videoWidth;
        canvas.height = cameraFeed.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(cameraFeed, 0, 0);
        
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        capturedImage.src = imageData;
        
        cameraFeed.style.display = 'none';
        capturedImage.style.display = 'block';
        captureBtn.style.display = 'none';
        
        // Stop camera
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        // Show confirmation dialog
        if (currentStep === 'front') {
            if (confirm('Front of ID captured. Is this image clear? Click OK to proceed to back of ID, or Cancel to retake.')) {
                frontImageData = imageData;
                idImageDataFront.value = imageData;
                // Move to back step
                currentStep = 'back';
                idCaptureTitle.innerHTML = '<i class="bi bi-card-checklist"></i> Back of ID';
                // Reset for back capture
                capturedImage.src = '';
                capturedImage.style.display = 'none';
                cameraPlaceholder.style.display = 'flex';
                retakeBtn.style.display = 'none';
                startCameraBtn.style.display = 'inline-block';
                uploadBtn.style.display = 'inline-block';
            } else {
                // Retake front
                retakeBtn.style.display = 'inline-block';
            }
        } else if (currentStep === 'back') {
            if (confirm('Back of ID captured. Is this image clear? Click OK to finish, or Cancel to retake.')) {
                backImageData = imageData;
                idImageDataBack.value = imageData;
                // Done - hide buttons
                retakeBtn.style.display = 'none';
                uploadBtn.style.display = 'none';
                idCaptureTitle.innerHTML = '<i class="bi bi-check-circle-fill"></i> ID Photos Complete';
            } else {
                // Retake back
                retakeBtn.style.display = 'inline-block';
            }
        }
    });

    // Retake photo
    retakeBtn.addEventListener('click', function() {
        if (currentStep === 'front') {
            frontImageData = null;
            idImageDataFront.value = '';
        } else {
            backImageData = null;
            idImageDataBack.value = '';
        }
        capturedImage.src = '';
        cameraPlaceholder.style.display = 'flex';
        capturedImage.style.display = 'none';
        retakeBtn.style.display = 'none';
        startCameraBtn.style.display = 'inline-block';
        uploadBtn.style.display = 'inline-block';
    });

    // Upload file
    uploadBtn.addEventListener('click', function() {
        idImageUpload.click();
    });

    idImageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imageData = event.target.result;
                capturedImage.src = imageData;
                cameraPlaceholder.style.display = 'none';
                capturedImage.style.display = 'block';
                startCameraBtn.style.display = 'none';
                uploadBtn.style.display = 'none';

                // Handle upload confirmation
                if (currentStep === 'front') {
                    if (confirm('Front of ID uploaded. Is this image clear? Click OK to proceed to back of ID, or Cancel to retake.')) {
                        frontImageData = imageData;
                        idImageDataFront.value = imageData;
                        currentStep = 'back';
                        idCaptureTitle.innerHTML = '<i class="bi bi-card-checklist"></i> Back of ID';
                        capturedImage.src = '';
                        capturedImage.style.display = 'none';
                        cameraPlaceholder.style.display = 'flex';
                        startCameraBtn.style.display = 'inline-block';
                        uploadBtn.style.display = 'inline-block';
                    } else {
                        retakeBtn.style.display = 'inline-block';
                    }
                } else if (currentStep === 'back') {
                    if (confirm('Back of ID uploaded. Is this image clear? Click OK to finish, or Cancel to retake.')) {
                        backImageData = imageData;
                        idImageDataBack.value = imageData;
                        retakeBtn.style.display = 'none';
                        uploadBtn.style.display = 'none';
                        idCaptureTitle.innerHTML = '<i class="bi bi-check-circle-fill"></i> ID Photos Complete';
                    } else {
                        retakeBtn.style.display = 'inline-block';
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* ── Auth layout overrides ── */
    body { padding-top: 0 !important; background: var(--bg-main) !important; }
    nav.navbar { display: none !important; }
    footer.footer { display: none !important; }

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Background orbs */
    .reg-orb {
        position: fixed;
        border-radius: 50%;
        pointer-events: none;
        z-index: 0;
    }
    .reg-orb-1 {
        width: 500px; height: 500px;
        top: -200px; right: -150px;
        animation: regOrb1 12s ease-in-out infinite;
    }
    .reg-orb-2 {
        width: 350px; height: 350px;
        bottom: -100px; left: -100px;
        animation: regOrb2 9s ease-in-out infinite;
    }
    [data-theme="dark"]  .reg-orb-1 { background: radial-gradient(circle, rgba(218,54,255,0.20) 0%, transparent 70%); }
    [data-theme="dark"]  .reg-orb-2 { background: radial-gradient(circle, rgba(244,63,142,0.15) 0%, transparent 70%); }
    [data-theme="light"] .reg-orb-1 { background: radial-gradient(circle, rgba(218,54,255,0.12) 0%, transparent 70%); }
    [data-theme="light"] .reg-orb-2 { background: radial-gradient(circle, rgba(244,63,142,0.10) 0%, transparent 70%); }

    @keyframes regOrb1 {
        0%,100% { transform: translate(0,0) scale(1); }
        50%     { transform: translate(-30px, 30px) scale(1.1); }
    }
    @keyframes regOrb2 {
        0%,100% { transform: translate(0,0) scale(1); }
        50%     { transform: translate(25px,-20px) scale(1.08); }
    }

    /* Register card */
    .register-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: 24px;
        box-shadow: var(--shadow-md);
        width: 100%;
        max-width: 600px;
        overflow: hidden;
        position: relative;
        z-index: 1;
        animation: cardSlideUp 0.45s ease both;
    }
    @keyframes cardSlideUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Card header */
    .register-header {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 60%, var(--accent-pink) 100%);
        padding: 2rem 2rem 1.75rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .register-header::before {
        content: '';
        position: absolute;
        width: 250px; height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        top: -80px; right: -60px;
        pointer-events: none;
    }

    /* Step dots */
    .step-dots {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        position: relative;
        z-index: 1;
    }
    .step-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: rgba(255,255,255,0.35);
        transition: all 0.3s ease;
    }
    .step-dot.active {
        width: 24px;
        border-radius: 4px;
        background: #fff;
    }

    .register-header-icon {
        width: 72px; height: 72px;
        border-radius: 20px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        position: relative;
        z-index: 1;
    }

    .register-header h2 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0.3rem;
        position: relative;
        z-index: 1;
    }
    .register-header p {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.72);
        margin: 0;
        position: relative;
        z-index: 1;
    }

    /* Card body */
    .register-body { padding: 2rem; }

    /* Field icon wrap */
    .field-icon-wrap { position: relative; }
    .field-icon-wrap .fi-icon {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
        z-index: 2;
        transition: color 0.2s ease;
    }
    .field-icon-wrap:focus-within .fi-icon { color: var(--primary); }

    .field-icon-wrap input,
    .field-icon-wrap select,
    .field-icon-wrap textarea {
        padding-left: 2.65rem;
        background: var(--bg-input);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        color: var(--text);
        font-size: 0.9rem;
        width: 100%;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.3s ease;
        outline: none;
    }
    .field-icon-wrap input,
    .field-icon-wrap select { height: 48px; }
    .field-icon-wrap textarea { padding-top: 0.7rem; resize: none; }
    .field-icon-wrap input::placeholder,
    .field-icon-wrap textarea::placeholder { color: var(--text-muted); opacity: 0.65; }

    .field-icon-wrap input:focus,
    .field-icon-wrap select:focus,
    .field-icon-wrap textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
        background: var(--bg-input);
        color: var(--text);
    }

    /* Section divider */
    .form-section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: var(--text-muted);
        margin-top: 0.5rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .form-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* Register button */
    .btn-register {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 6px 24px var(--primary-glow);
        position: relative;
        overflow: hidden;
    }
    .btn-register::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.5s ease;
    }
    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 36px var(--primary-glow);
    }
    .btn-register:hover::after { transform: translateX(100%); }
    .btn-register:active { transform: scale(0.97); }

    /* Footer */
    .register-footer {
        text-align: center;
        padding: 1.25rem 2rem;
        border-top: 1px solid var(--border);
        font-size: 0.875rem;
        color: var(--text-muted);
        background: var(--bg-card2);
        transition: background var(--transition-slow);
    }
    .register-footer a {
        color: var(--primary-light);
        font-weight: 600;
        text-decoration: none;
    }
    .register-footer a:hover { color: var(--primary); }

    @media (max-width: 480px) {
        .register-wrapper { padding: 1.5rem 1rem; }
        .register-body { padding: 1.5rem; }
        .register-header { padding: 1.5rem 1.5rem 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="register-wrapper">
    <div class="reg-orb reg-orb-1"></div>
    <div class="reg-orb reg-orb-2"></div>

    <div class="register-card">

        {{-- Header --}}
        <div class="register-header">
            {{-- Progress dots (decorative) --}}
            <div class="step-dots">
                <div class="step-dot active"></div>
                <div class="step-dot"></div>
                <div class="step-dot"></div>
            </div>
            <div class="register-header-icon">
                <i class="bi bi-heart-pulse-fill text-white"></i>
            </div>
            <h2>Join ReproCare</h2>
            <p>Create your free account to start tracking your health</p>
        </div>

        {{-- Body --}}
        <div class="register-body">

            @if ($errors->any())
                <div class="alert alert-danger mb-4 d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill mt-1 flex-shrink-0"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <div class="row g-3">

                    {{-- Personal Info --}}
                    <div class="col-12">
                        <div class="form-section-label">
                            <i class="bi bi-person-fill" style="color: var(--primary-light);"></i>
                            Personal Information
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="first_name" class="form-label">First Name</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-person fi-icon"></i>
                            <input type="text"
                                   id="first_name"
                                   name="first_name"
                                   value="{{ old('first_name') }}"
                                   placeholder="First name"
                                   required
                                   autofocus
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="middle_initial" class="form-label">Middle Initial</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-person fi-icon"></i>
                            <input type="text"
                                   id="middle_initial"
                                   name="middle_initial"
                                   value="{{ old('middle_initial') }}"
                                   placeholder="M.I."
                                   maxlength="2"
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label for="last_name" class="form-label">Last Name</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-person fi-icon"></i>
                            <input type="text"
                                   id="last_name"
                                   name="last_name"
                                   value="{{ old('last_name') }}"
                                   placeholder="Last name"
                                   required
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-envelope fi-icon"></i>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="your@email.com"
                                   required
                                   autocomplete="off"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">Birthdate</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-calendar-heart fi-icon"></i>
                            <input type="date"
                                   id="date_of_birth"
                                   name="date_of_birth"
                                   value="{{ old('date_of_birth') }}"
                                   max="{{ now()->subDay()->format('Y-m-d') }}"
                                   required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Phone Number</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-telephone fi-icon"></i>
                            <input type="text"
                                   id="contact_number"
                                   name="contact_number"
                                   value="{{ old('contact_number') }}"
                                   placeholder="09XXXXXXXXX"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Partner Contact --}}
                    <div class="col-12 mt-1">
                        <div class="form-section-label">
                            <i class="bi bi-people-fill" style="color: var(--primary-light);"></i>
                            Partner Details (Optional)
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="partner_name" class="form-label">Partner/Spouse Name</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-person-hearts fi-icon"></i>
                            <input type="text"
                                   id="partner_name"
                                   name="partner_name"
                                   value="{{ old('partner_name') }}"
                                   placeholder="Full Name"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="partner_contact" class="form-label">Partner Phone Number</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-telephone-plus fi-icon"></i>
                            <input type="text"
                                   id="partner_contact"
                                   name="partner_contact"
                                   value="{{ old('partner_contact') }}"
                                   placeholder="09XXXXXXXXX"
                                   autocomplete="off">
                        </div>
                    </div>

                    {{-- Security --}}
                    <div class="col-12 mt-1">
                        <div class="form-section-label">
                            <i class="bi bi-shield-lock-fill" style="color: var(--primary-light);"></i>
                            Security
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-lock fi-icon"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   placeholder="Min. 8 characters"
                                   required
                                   autocomplete="new-password"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-lock-fill fi-icon"></i>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Repeat password"
                                   required
                                   autocomplete="new-password"
                                   readonly
                                   onfocus="this.removeAttribute('readonly')">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="col-12 mt-1">
                        <div class="form-section-label">
                            <i class="bi bi-geo-alt-fill" style="color: var(--primary-light);"></i>
                            Location
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Barangay</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-geo-alt fi-icon"></i>
                            <input type="text"
                                   value="{{ $barangay }}"
                                   readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="purok_id" class="form-label">Purok</label>
                        <div class="field-icon-wrap">
                            <i class="bi bi-signpost-2 fi-icon"></i>
                            <select id="purok_id" name="purok_id" required>
                                <option value="">Select your purok</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->id }}" {{ old('purok_id') == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Emergency Contacts --}}
                    <div class="col-12 mt-1">
                        <div class="form-section-label">
                            <i class="bi bi-people-fill" style="color: var(--primary-light);"></i>
                            Emergency Contact Persons
                        </div>
                    </div>

                    {{-- Primary Emergency Contact --}}
                    <div class="col-12">
                        <div class="card p-3 border-0" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass) !important; border-radius: 12px;">
                            <h6 class="text-white mb-3" style="font-size: 0.85rem; font-weight: 700; color: var(--primary-light) !important;">
                                <i class="bi bi-1-circle-fill"></i> Primary Contact (Required)
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="emergency_name_1" class="form-label">Full Name</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-person fi-icon"></i>
                                        <input type="text" id="emergency_name_1" name="emergency_name_1" value="{{ old('emergency_name_1') }}" placeholder="Contact's full name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_relationship_1" class="form-label">Relationship</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-heart fi-icon"></i>
                                        <input type="text" id="emergency_relationship_1" name="emergency_relationship_1" value="{{ old('emergency_relationship_1') }}" placeholder="Spouse, Mother, Friend, etc." required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact_number_1" class="form-label">Contact Number</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-telephone fi-icon"></i>
                                        <input type="text" id="emergency_contact_number_1" name="emergency_contact_number_1" value="{{ old('emergency_contact_number_1') }}" placeholder="09XXXXXXXXX" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_address_1" class="form-label">Address</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-geo-alt fi-icon"></i>
                                        <input type="text" id="emergency_address_1" name="emergency_address_1" value="{{ old('emergency_address_1') }}" placeholder="Address (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Secondary Emergency Contact --}}
                    <div class="col-12 mt-2">
                        <div class="card p-3 border-0" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass) !important; border-radius: 12px;">
                            <h6 class="text-white mb-3" style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted) !important;">
                                <i class="bi bi-2-circle-fill"></i> Secondary Contact (Optional)
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="emergency_name_2" class="form-label">Full Name</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-person fi-icon"></i>
                                        <input type="text" id="emergency_name_2" name="emergency_name_2" value="{{ old('emergency_name_2') }}" placeholder="Contact's full name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_relationship_2" class="form-label">Relationship</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-heart fi-icon"></i>
                                        <input type="text" id="emergency_relationship_2" name="emergency_relationship_2" value="{{ old('emergency_relationship_2') }}" placeholder="Spouse, Mother, Friend, etc.">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact_number_2" class="form-label">Contact Number</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-telephone fi-icon"></i>
                                        <input type="text" id="emergency_contact_number_2" name="emergency_contact_number_2" value="{{ old('emergency_contact_number_2') }}" placeholder="09XXXXXXXXX">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_address_2" class="form-label">Address</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-geo-alt fi-icon"></i>
                                        <input type="text" id="emergency_address_2" name="emergency_address_2" value="{{ old('emergency_address_2') }}" placeholder="Address (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tertiary Emergency Contact --}}
                    <div class="col-12 mt-2">
                        <div class="card p-3 border-0" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass) !important; border-radius: 12px;">
                            <h6 class="text-white mb-3" style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted) !important;">
                                <i class="bi bi-3-circle-fill"></i> Tertiary Contact (Optional)
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="emergency_name_3" class="form-label">Full Name</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-person fi-icon"></i>
                                        <input type="text" id="emergency_name_3" name="emergency_name_3" value="{{ old('emergency_name_3') }}" placeholder="Contact's full name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_relationship_3" class="form-label">Relationship</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-heart fi-icon"></i>
                                        <input type="text" id="emergency_relationship_3" name="emergency_relationship_3" value="{{ old('emergency_relationship_3') }}" placeholder="Spouse, Mother, Friend, etc.">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_contact_number_3" class="form-label">Contact Number</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-telephone fi-icon"></i>
                                        <input type="text" id="emergency_contact_number_3" name="emergency_contact_number_3" value="{{ old('emergency_contact_number_3') }}" placeholder="09XXXXXXXXX">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_address_3" class="form-label">Address</label>
                                    <div class="field-icon-wrap">
                                        <i class="bi bi-geo-alt fi-icon"></i>
                                        <input type="text" id="emergency_address_3" name="emergency_address_3" value="{{ old('emergency_address_3') }}" placeholder="Address (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Valid ID Photo --}}
                    <div class="col-12 mt-1">
                        <div class="form-section-label">
                            <i class="bi bi-card-image" style="color: var(--primary-light);"></i>
                            Valid ID Photo
                        </div>
                    </div>

                    {{-- ID Capture Section --}}
                    <div class="col-12">
                        <div class="card" style="background: var(--bg-input); border: 1.5px solid var(--border); border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 id="id-capture-title" style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--primary-light);">
                                    <i class="bi bi-card-checklist"></i> Front of ID
                                </h6>
                                <div class="d-flex align-items-center gap-3">
                                    <div id="camera-preview" style="width: 200px; height: 150px; background: #000; border-radius: 8px; overflow: hidden; position: relative; flex-shrink: 0;">
                                        <video id="camera-feed" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; display: none;"></video>
                                        <img id="captured-image" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        <div id="camera-placeholder" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted);">
                                            <i class="bi bi-camera" style="font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                                            <span style="font-size: 0.75rem;">No image</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <button type="button" id="start-camera-btn" class="btn btn-sm" style="background: var(--primary); color: #fff; border: none; border-radius: 8px;">
                                                <i class="bi bi-camera-fill"></i> Open Camera
                                            </button>
                                            <button type="button" id="capture-btn" class="btn btn-sm" style="background: var(--success); color: #fff; border: none; border-radius: 8px; display: none;">
                                                <i class="bi bi-camera-fill"></i> Capture
                                            </button>
                                            <button type="button" id="retake-btn" class="btn btn-sm" style="background: var(--warning); color: #fff; border: none; border-radius: 8px; display: none;">
                                                <i class="bi bi-arrow-counterclockwise"></i> Retake
                                            </button>
                                            <button type="button" id="upload-btn" class="btn btn-sm" style="background: var(--secondary); color: #fff; border: none; border-radius: 8px;">
                                                <i class="bi bi-upload"></i> Upload File
                                            </button>
                                            <input type="file" id="id-image-upload" accept="image/*" style="display: none;">
                                        </div>
                                        <input type="hidden" id="id_image_data_front" name="id_image_data_front">
                                        <input type="hidden" id="id_image_data_back" name="id_image_data_back">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn-register mt-4">
                    <i class="bi bi-person-plus-fill"></i>
                    Create Account
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="register-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
        </div>

    </div>
</div>
@endsection
