@extends(request()->routeIs('midwife.messages.*') ? 'midwife.layout' : (request()->routeIs('bhw.messages.*') ? 'bhw.layout' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president.layout' : 'user.layout')))

@section('title', 'New Chat - ReproCare')

@push('styles')
<style>
    body { padding-top: 70px !important; }
    .compose-shell {
        display: grid;
        grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
        gap: 1.25rem;
    }
    .compose-panel {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    .compose-head {
        padding: 1.2rem 1.35rem;
        border-bottom: 1px solid var(--border);
        background: linear-gradient(135deg, rgba(61, 102, 255, 0.12), rgba(17, 24, 39, 0.04));
    }
    .compose-title {
        margin: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text);
    }
    .compose-subtitle {
        margin-top: 0.3rem;
        font-size: 0.82rem;
        color: var(--text-muted);
    }
    .contact-search {
        width: 100%;
        margin-top: 0.9rem;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 14px;
        color: var(--text);
        padding: 0.85rem 1rem;
        outline: none;
    }
    .contact-search:focus,
    .chat-input:focus,
    .chat-textarea:focus,
    .schedule-field:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
    .contact-list {
        padding: 0.8rem;
        max-height: 72vh;
        overflow-y: auto;
    }
    .contact-option {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        border-radius: 18px;
        text-align: left;
        padding: 0.9rem;
        color: var(--text);
        margin-bottom: 0.6rem;
        transition: 0.18s ease;
    }
    .contact-option:hover,
    .contact-option.active {
        background: rgba(61, 102, 255, 0.06);
        border-color: rgba(61, 102, 255, 0.16);
    }
    .contact-avatar {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }
    .contact-badge {
        display: inline-flex;
        margin-top: 0.18rem;
        font-size: 0.72rem;
        padding: 0.18rem 0.55rem;
        border-radius: 999px;
        color: var(--text-muted);
        background: var(--bg-card2);
    }
    .chat-form {
        padding: 1.2rem;
    }
    .chat-input,
    .chat-textarea,
    .schedule-field {
        width: 100%;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 16px;
        color: var(--text);
        padding: 0.9rem 1rem;
        outline: none;
        transition: 0.18s ease;
    }
    .chat-textarea {
        min-height: 260px;
        resize: vertical;
    }
    .selected-contact {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--border);
        background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(61, 102, 255, 0.05));
        margin-bottom: 1rem;
    }
    .hidden {
        display: none !important;
    }
    @media (max-width: 991.98px) {
        .compose-shell {
            grid-template-columns: 1fr;
        }
        .contact-list {
            max-height: none;
        }
    }
</style>
@endpush

@php
    $contentSection = request()->routeIs('midwife.messages.*') ? 'midwife-content' : (request()->routeIs('bhw.messages.*') ? 'bhw-content' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president-content' : 'user-content'));
    $messagesRouteBase = request()->routeIs('midwife.messages.*') ? 'midwife.messages' : (request()->routeIs('bhw.messages.*') ? 'bhw.messages' : (request()->routeIs('bhw-president.messages.*') ? 'bhw-president.messages' : 'user.messages'));
    $selectedReceiverId = old('receiver_id', $receiver?->id);
    $selectedReceiverRole = old('receiver_role', $receiver?->role);
@endphp

@section($contentSection)
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:800;color:var(--text);">
            <i class="bi bi-pencil-square me-2"></i>New Chat
        </h2>
        <div style="color:var(--text-muted);font-size:0.9rem;">
            Choose a contact, then send a message like a modern chat app.
        </div>
    </div>
    <a href="{{ route($messagesRouteBase . '.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Messages
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="compose-shell">
    <section class="compose-panel">
        <div class="compose-head">
            <h3 class="compose-title">Contacts</h3>
            <div class="compose-subtitle">
                @if(auth()->user()?->role === 'user')
                    Patients can message midwives, BHWs, and BHW presidents only.
                @else
                    Staff can message any user in the system.
                @endif
            </div>
            <input type="search" id="contactSearch" class="contact-search" placeholder="Search contacts..." value="{{ $search ?? '' }}">
        </div>

        <div class="contact-list" id="contactList">
            @forelse($contacts as $contact)
                @php
                    $isActive = (string) $selectedReceiverId === (string) $contact->id && $selectedReceiverRole === $contact->role;
                @endphp
                <button type="button"
                        class="contact-option {{ $isActive ? 'active' : '' }}"
                        data-id="{{ $contact->id }}"
                        data-role="{{ $contact->role }}"
                        data-name="{{ $contact->name }}"
                        data-label="{{ $contact->label }}"
                        data-search="{{ strtolower($contact->name . ' ' . $contact->label . ' ' . $contact->role) }}">
                    <div class="contact-avatar">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
                    <div>
                        <div class="fw-semibold">{{ $contact->name }}</div>
                        <span class="contact-badge">{{ $contact->label }}</span>
                    </div>
                </button>
            @empty
                <div class="p-4 text-center text-muted">
                    No contacts available.
                </div>
            @endforelse
        </div>
    </section>

    <section class="compose-panel">
        <div class="compose-head">
            <h3 class="compose-title">Message Composer</h3>
            <div class="compose-subtitle">Start a direct conversation.</div>
        </div>

        <form action="{{ route($messagesRouteBase . '.send') }}" method="POST" class="chat-form">
            @csrf
            <input type="hidden" name="receiver_role" id="receiver_role" value="{{ $selectedReceiverRole }}">
            <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $selectedReceiverId }}">

            <div class="selected-contact" id="selectedContactCard">
                <div class="contact-avatar" id="selectedContactAvatar">
                    {{ $receiver ? strtoupper(substr($receiver->name, 0, 1)) : '?' }}
                </div>
                <div>
                    <div class="fw-semibold" id="selectedContactName">{{ $receiver?->name ?? 'No contact selected' }}</div>
                    <span class="contact-badge" id="selectedContactLabel">{{ $receiver ? ucfirst(str_replace('_', ' ', $receiver->role)) : 'Choose a contact from the left' }}</span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-uppercase fw-semibold text-muted">Subject</label>
                <input type="text" name="subject" class="chat-input" placeholder="Optional subject..." value="{{ old('subject') }}">
            </div>

            <div class="mb-3">
                <label class="form-label small text-uppercase fw-semibold text-muted">Message</label>
                <textarea name="body" class="chat-textarea" placeholder="Type your message..." required>{{ old('body') }}</textarea>
            </div>


            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route($messagesRouteBase . '.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill me-1"></i> Send Message
                </button>
            </div>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contactButtons = Array.from(document.querySelectorAll('.contact-option'));
    const contactSearch = document.getElementById('contactSearch');
    const receiverIdInput = document.getElementById('receiver_id');
    const receiverRoleInput = document.getElementById('receiver_role');
    const selectedContactAvatar = document.getElementById('selectedContactAvatar');
    const selectedContactName = document.getElementById('selectedContactName');
    const selectedContactLabel = document.getElementById('selectedContactLabel');
    function setActiveContact(button) {
        contactButtons.forEach(function (item) {
            item.classList.remove('active');
        });

        if (!button) {
            receiverIdInput.value = '';
            receiverRoleInput.value = '';
            selectedContactAvatar.textContent = '?';
            selectedContactName.textContent = 'No contact selected';
            selectedContactLabel.textContent = 'Choose a contact from the left';
            return;
        }

        button.classList.add('active');
        receiverIdInput.value = button.dataset.id;
        receiverRoleInput.value = button.dataset.role;
        selectedContactAvatar.textContent = (button.dataset.name || '?').charAt(0).toUpperCase();
        selectedContactName.textContent = button.dataset.name || 'Unknown User';
        selectedContactLabel.textContent = button.dataset.label || button.dataset.role || '';
    }

    contactButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            setActiveContact(button);
        });
    });

    if (contactSearch) {
        contactSearch.addEventListener('input', function () {
            const needle = this.value.toLowerCase().trim();

            contactButtons.forEach(function (button) {
                const haystack = button.dataset.search || '';
                button.classList.toggle('hidden', needle !== '' && !haystack.includes(needle));
            });
        });
    }

    const activeButton = contactButtons.find(function (button) {
        return button.classList.contains('active');
    });

    setActiveContact(activeButton || null);
});
</script>
@endsection
