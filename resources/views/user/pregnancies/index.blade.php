@extends('user.layout')

@section('title', 'My Pregnancies - ReproCare')

@section('user-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-heart-pulse"></i> My Pregnancies</h1>
        @if(!$pregnancies->firstWhere('is_active', true))
            <a href="{{ route('user.pregnancies.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Pregnancy
            </a>
        @endif
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row">
        @if($pregnancies->count() > 0)
            @foreach($pregnancies as $pregnancy)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge {{ $pregnancy->edd >= now() ? 'bg-success' : 'bg-secondary' }}">
                                {{ $pregnancy->edd >= now() ? 'Active' : 'Completed' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Pregnancy Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Last Menstrual Period:</strong><br>{{ $pregnancy->lmp ? $pregnancy->lmp->format('M j, Y') : 'N/A' }}</p>
                                    <p><strong>Estimated Due Date:</strong><br>{{ $pregnancy->edd ? $pregnancy->edd->format('M j, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Gravida:</strong> {{ $pregnancy->gravida }}</p>
                                    <p><strong>Para:</strong> {{ $pregnancy->para }}</p>
                                    <p><strong>Risk:</strong> {{ $pregnancy->risk_level ?? 'Low' }}</p>
                                </div>
                            </div>
                            @if($pregnancy->edd >= now())
                                <div class="mt-3">
                                    <span class="badge bg-info">{{ $pregnancy->formatted_aog }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Recorded on {{ $pregnancy->created_at ? $pregnancy->created_at->format('M j, Y') : 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                    <h3 class="text-muted mt-3">No pregnancy records</h3>
                    <p class="text-muted">Start tracking your pregnancy journey today.</p>
                    <a href="{{ route('user.pregnancies.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add First Pregnancy
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
