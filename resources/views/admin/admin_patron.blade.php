@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
             <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
     <li class="breadcrumb-item active" aria-current="page">Manage</li>
      <li class="breadcrumb-item active" aria-current="page">Patrons</li>
  </ol>
</nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Patrons</h4>
                    </div>
                    <div class="card-body">

                        <!-- Modal -->
                        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered " role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title">
                                            <span class="fw-light">
                                                Patron Profile
                                            </span>
                                        </h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form>
                                            <div class="row">

                                                <div class="col-sm-12 d-flex justify-content-center">
                                                    <div class="input-file input-file-image">
                                                        <img id="view-avatar" class="img-upload-preview" width="100"
                                                            height="100" src="{{ asset('default.png') }}" alt="preview">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input id="view-name" type="text" class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 ">
                                                    <div class="form-group ">
                                                        <label>Email</label>
                                                        <input id="view-email" type="text" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Status</label>
                                                        <p class="mb-0">
                                                            <span id="view-status-badge"
                                                                class="badge bg-secondary">Unknown</span>
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>AVATAR</th>
                                        <th>NAME</th>
                                        <th>EMAIL</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($patrons as $index => $patron)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($patron->avatar)
                                                    <img src="{{ asset('storage/' . $patron->avatar) }}" width="40"
                                                        height="40" class="rounded-circle">
                                                @else
                                                    <img src="" width="40" height="40"
                                                        class="rounded-circle">
                                                @endif
                                            </td>
                                            <td>{{ $patron->first_name }} {{ $patron->last_name }}</td>
                                            <td>{{ $patron->email }}</td>
                                            {{-- <td>{{ ucfirst($patron->gender) }}</td> --}}
                                            <td class="text-center">
                                                {{-- Display status with badge --}}
                                                <span
                                                    class="badge {{ $patron->status == 'verified' ? 'bg-success' : 'bg-danger' }} px-2 py-1"
                                                    style="font-size: 1rem;">
                                                    {{ ucfirst($patron->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">

                                                    @if ($patron->status === 'unverified')
                                                        <form action="{{ route('admin.patron.verify', $patron->id) }}"
                                                            method="POST" style="display:inline;" class="me-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Verify">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="javascript:void(0);"
                                                        class="btn btn-sm btn-primary btn-view-patron" title="View"
                                                        data-bs-toggle="modal" data-bs-target="#viewModal"
                                                        data-first_name="{{ $patron->first_name }}"
                                                        data-last_name="{{ $patron->last_name }}"
                                                        data-email="{{ $patron->email }}"
                                                        data-avatar="{{ $patron->avatar ? asset('storage/' . $patron->avatar) : asset('default.png') }}"
                                                        data-status="{{ $patron->status }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <!-- Delete Button -->
                                                    <form id="delete-form-{{ $patron->id }}"
                                                        action="{{ route('admin.patron.destroy', $patron->id) }}"
                                                        method="POST" style="display:inline;" class="me-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $patron->id }}" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
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
            $('#basic-datatables').DataTable({});

        });


        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let patronId = $(this).data('id');

            swal({
                title: "Are you sure you want to delete this patron?",
                text: "This action cannot be undone.",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "btn btn-danger"
                    },
                    confirm: {
                        text: "Yes, delete!",
                        className: "btn btn-success"
                    }
                }
            }).then((willDelete) => {
                if (willDelete) {
                    document.getElementById(`delete-form-${patronId}`).submit();
                }
            });
        });
    </script>


    @if (session('verified_success'))
        <script>
            swal("Good job!", "Verified successfully!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
    @endif

    @if (session('destroy_success'))
        <script>
            swal("Good job!", "deleted successfully!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('.btn-view-patron').on('click', function() {
                let firstName = $(this).data('first_name');
                let lastName = $(this).data('last_name');
                let email = $(this).data('email');
                let avatar = $(this).data('avatar');
                let status = $(this).data('status'); 

                // fill into modal
                $('#view-name').val(firstName + ' ' + lastName);
                $('#view-email').val(email);
                $('#view-avatar').attr('src', avatar);

                // update status badge
                let badge = $('#view-status-badge');
                badge.removeClass('bg-success bg-warning bg-danger bg-secondary text-dark');

                if (status === 'verified') {
                    badge.addClass('bg-success');
                    badge.text('Verified');
                } else if (status === 'pending') {
                    badge.addClass('bg-warning text-dark');
                    badge.text('Pending');
                } else if (status === 'rejected') {
                    badge.addClass('bg-danger');
                    badge.text('Rejected');
                } else {
                    badge.addClass('bg-secondary');
                    badge.text('Unknown');
                }
            });
        });
    </script>
    </script>

@endpush
