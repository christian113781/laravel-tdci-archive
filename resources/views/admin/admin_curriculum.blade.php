@extends('admin.admin_dashboard')

@section('curriculum')
    <div class="page-inner">

        <div class="page-header">
						<h3 class="fw-bold mb-3">DataTables.Net</h3>
						<ul class="breadcrumbs mb-3">
							<li class="nav-home">
								<a href="#">
									<i class="icon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="icon-arrow-right"></i>
							</li>
							<li class="nav-item">
								<a href="#">Tables</a>
							</li>
							<li class="separator">
								<i class="icon-arrow-right"></i>
							</li>
							<li class="nav-item">
								<a href="#">Datatables</a>
							</li>
						</ul>
					</div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">List of Curriculum</h4>
                            <a href="#" class="btn btn-primary btn-round ms-auto btn-add-curriculum">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Curriculum Modal -->
                        <div class="modal fade" id="curriculumModal" tabindex="-1">
                             <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitle">Add Curriculum</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form id="curriculumForm" method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" id="formMethod" value="POST">

                                            {{-- Department Select --}}
                                            <div class="mb-3">
                                                <label class="form-label">Select Department</label>
                                                <select class="form-select" name="department_id" id="department_id"
                                                    required>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Course Name --}}
                                            <div class="mb-3">
                                                <label class="form-label">Course Name</label>
                                                <input type="text" name="name" id="course_name" class="form-control"
                                                    placeholder="Fill course name" required>
                                            </div>

                                            {{-- Description --}}
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" name="description" id="description"
                                                    class="form-control" placeholder="Fill description">
                                            </div>

                                            <div class="modal-footer border-0">
                                                <button type="submit" class="btn btn-primary"
                                                    id="modalSubmitBtn">Add</button>
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
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>NAME</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>NAME</th>
                                        <th>DESCRIPTION</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($curriculums as $curriculum)
                                        <tr>
                                            <td>{{ $curriculum->id }}</td>
                                            <td>{{ $curriculum->name }}</td>
                                            <td>{{ $curriculum->description }}</td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <!-- Edit Button -->
                                                    <a href="#" class="btn btn-sm btn-primary btn-edit-curriculum"
                                                        data-id="{{ $curriculum->id }}"
                                                        data-department="{{ $curriculum->department_id }}"
                                                        data-name="{{ $curriculum->name }}"
                                                        data-description="{{ $curriculum->description }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <!-- Delete Button for Curriculum -->
                                                    <form id="delete-form-curriculum-{{ $curriculum->id }}"
                                                        action="{{ route('admin.curriculum.destroy', $curriculum->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-delete-curriculum"
                                                            data-id="{{ $curriculum->id }}">
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

@push('curriculum-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#basic-datatables').DataTable({});

            // Add Curriculum
            document.querySelector('.btn-add-curriculum').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modalTitle').textContent = 'Add Curriculum';
                document.getElementById('curriculumForm').action = '{{ route('admin.curriculum.store') }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('modalSubmitBtn').textContent = 'Add';

                document.getElementById('department_id').value = '';
                document.getElementById('course_name').value = '';
                document.getElementById('description').value = '';

                new bootstrap.Modal(document.getElementById('curriculumModal')).show();
            });

            // Edit Curriculum
            document.querySelectorAll('.btn-edit-curriculum').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('modalTitle').textContent = 'Edit Curriculum';
                    document.getElementById('curriculumForm').action =
                        `/admin/curriculum/${this.dataset.id}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('modalSubmitBtn').textContent = 'Update';

                    document.getElementById('department_id').value = this.dataset.department;
                    document.getElementById('course_name').value = this.dataset.name;
                    document.getElementById('description').value = this.dataset.description;

                    new bootstrap.Modal(document.getElementById('curriculumModal')).show();
                });
            });
        });



        // Curriculum Delete SweetAlert
        $(document).on('click', '.btn-delete-curriculum', function(e) {
            e.preventDefault();

            let curriculumId = $(this).data('id');

            swal({
                title: "Are you sure you want to delete this curriculum?",
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
                    document.getElementById(`delete-form-curriculum-${curriculumId}`).submit();
                }
            });
        });
    </script>

    @if (session('success_alert_delete'))
        <script>
            swal("Good job!", "Curriculum delete successfully!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
    @endif

    @if (session('success_alert'))
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

    @if (session('success_alert_update'))
        <script>
            swal("Good job!", "Curriculum update successfully!", {
                icon: "success",
                buttons: {
                    confirm: {
                        className: 'btn btn-success'
                    }
                },
            });
        </script>
    @endif
@endpush
