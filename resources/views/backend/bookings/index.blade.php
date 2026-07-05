@extends('layouts.dashboard')
@section('title', 'Party Bookings Management')

@section('content')
    <x-breadcrumb></x-breadcrumb>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3" style="background:#f8f9fb; border-bottom:1px solid #e9ecef;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-bold"><i class="ri-calendar-event-line me-2"></i> Party Bookings</h5>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Members</th>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th class="text-center" width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->name }}</td>
                                        <td>{{ $booking->phone }}</td>
                                        <td>{{ $booking->total_members }}</td>
                                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                        <td>{{ $booking->branch->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($booking->status == 'pending')
                                                <span class="badge bg-warning text-dark"><i class="ri-time-line me-1"></i>Pending</span>
                                            @elseif($booking->status == 'due')
                                                <span class="badge bg-danger"><i class="ri-error-warning-line me-1"></i>Due</span>
                                            @elseif($booking->status == 'partial_due')
                                                <span class="badge bg-info"><i class="ri-pie-chart-2-line me-1"></i>Partial Due</span>
                                            @elseif($booking->status == 'paid')
                                                <span class="badge bg-success"><i class="ri-checkbox-circle-line me-1"></i>Paid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editBookingModal{{ $booking->id }}" title="Edit Booking">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <form action="{{ route('admin.party-bookings.delete', $booking->id) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')" title="Delete Booking">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editBookingModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="ri-edit-box-line me-2"></i>Update Booking Status
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.party-bookings.update', $booking->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="due" {{ $booking->status == 'due' ? 'selected' : '' }}>Due</option>
                                                                <option value="partial_due" {{ $booking->status == 'partial_due' ? 'selected' : '' }}>Partial Due</option>
                                                                <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Admin Note</label>
                                                            <textarea name="note" class="form-control" rows="3" placeholder="Add an internal note about this booking...">{{ $booking->note }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No bookings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($bookings->hasPages())
                        <div class="mt-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
