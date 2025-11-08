@extends('staff.staff_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
    <li class="breadcrumb-item active" aria-current="page">Archive</li>
    <li class="breadcrumb-item active" aria-current="page">Request</li>
  </ol>
</nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Archive Request</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>EMAIL</th>
                                        <th>TITLE</th>
                                        <th>REQUEST DATE</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach ($requests as $req)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                {{ $req->user->first_name }} {{ $req->user->last_name }}
                                            </td>
                                            <td>{{ $req->user->email }}</td>
                                            <td style="text-align: left;">
                                                {{ $req->archive->title }}
                                            </td>
                                            
                                            <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                @if ($req->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($req->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    @if ($req->status === 'pending')
                                                        {{-- Show both Approve & Reject --}}
                                                        <a href="{{ route('staff.archive.request.approve', $req->id) }}"
                                                            class="btn btn-sm btn-success approve-btn" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="{{ route('staff.archive.request.reject', $req->id) }}"
                                                            class="btn btn-sm btn-danger reject-btn" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @elseif ($req->status === 'approved')
                                                        {{-- If approved → hide Approve, show only Reject --}}
                                                        <a href="{{ route('staff.archive.request.reject', $req->id) }}"
                                                            class="btn btn-sm btn-danger reject-btn" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @elseif ($req->status === 'rejected')
                                                        {{-- If rejected → hide Reject, show only Approve --}}
                                                        <a href="{{ route('staff.archive.request.approve', $req->id) }}"
                                                            class="btn btn-sm btn-success approve-btn" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#basic-datatables').DataTable({
                
            });
            

        });


        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".approve-btn").forEach(function(button) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    let url = this.getAttribute("href");

                    // Show SweetAlert loader
                    swal({
                        title: "Processing...",
                        text: "Sending approval email...",
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        icon: "warning"
                    });

                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = url;
                    }, 800);
                });
            });
        });


         document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".reject-btn").forEach(function(button) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    let url = this.getAttribute("href");

                    // Show SweetAlert loader
                    swal({
                        title: "Processing...",
                        text: "Sending approval email...",
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        icon: "warning"
                    });

                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = url;
                    }, 800);
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            swal("Success!", "{{ session('success') }}", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                }
            });
        </script>
    @endif



    @if (session('error'))
        <script>
            swal("Error!", "{{ session('error') }}", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: 'btn btn-danger'
                    }
                }
            });
        </script>
    @endif
@endpush
