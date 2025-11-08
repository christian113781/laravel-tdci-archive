@extends('staff.staff_dashboard')

@section('pages')
    <div class="page-inner">

        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Manage</li>
                    <li class="breadcrumb-item active" aria-current="page">Program</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Programs</h4>

                            <button class="btn btn-primary btn-round ms-auto" type="button" onclick="openAddModal()">
                                <i class="fa fa-plus"></i>
                                Add New
                            </button>

                        </div>
                    </div>
                    <div class="card-body">


                        <!-- Modal -->
                        <div class="modal fade" id="addRowModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="modalTitle">
                                            <span class="fw-mediumbold">New Program</span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('staff.program.store') }}" method="POST" id="programForm">
                                            @csrf
                                            <input type="hidden" name="_method" id="formMethod" value="POST">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Name</label>
                                                        <input id="addName" name="name" type="text"
                                                            class="form-control" placeholder="Enter Program Name" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-0">
                                                <button type="submit" id="addRowButton"
                                                    class="btn btn-primary">Save</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- End Modal -->

                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>DATE CREATED</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                               
                                <tbody>
                                    @foreach ($programs as $index => $program)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $program->name }}</td>
                                            <td>{{ $program->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="openEditModal({{ $program->id }}, '{{ addslashes($program->name) }}')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <form id="delete-form-{{ $program->id }}"
                                                        action="{{ route('staff.program.destroy', $program->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $program->id }}">
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
            $('#basic-datatables').DataTable();

            @if (session('success'))
                swal("Good job!", "{{ session('success') }}", {
                    icon: "success",
                    buttons: {
                        confirm: {
                            className: 'btn btn-success'
                        }
                    },
                });
            @endif
        });

        function openAddModal() {
            document.getElementById('programForm').action = '{{ route('staff.program.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('addName').value = '';
            document.getElementById('modalTitle').textContent = 'Add Program';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        function openEditModal(id, name) {
            document.getElementById('programForm').action = `/staff/program/${id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('addName').value = name;
            document.getElementById('modalTitle').textContent = 'Edit Program';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        $('#addRowModal').on('hidden.bs.modal', function() {
            document.getElementById('programForm').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').textContent = 'Add Program';
        });



        // Disable submit to prevent multiple clicks
        $('#programForm').on('submit', function() {
            const btn = $('#addRowButton');
            btn.prop('disabled', true).text('Saving');
        });


        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let programId = $(this).data('id');

            swal({
                title: "Are you sure you want to delete this program?",
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
                    document.getElementById(`delete-form-${programId}`).submit();
                }
            });
        });
    </script>
@endpush
