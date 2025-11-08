@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">

        <div class="page-header">
            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
    <li class="breadcrumb-item active" aria-current="page">Manage</li>
    <li class="breadcrumb-item active" aria-current="page">Announcements</li>
  </ol>
</nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Announcement</h4>
                            <button class="btn btn-primary btn-round ms-auto" type="button" onclick="openAddModal()">
                                <i class="fa fa-plus"></i>
                                Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Modal -->
                        <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="modalTitle">Announcement</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="AnnouncementForm" action="{{ route('admin.announcement.store') }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" id="formMethod" value="POST">

                                            <div class="row">
                                                <!-- Title -->
                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <label for="addTitle">Title</label>

                                                        <input type="text" id="addTitle" name="title"
                                                            class="form-control" required>
                                                    </div>
                                                </div>

                                                <!-- Message -->
                                                <div class="col-sm-12">
                                                    <div class="form-group t">
                                                        <label for="addMessage">Message</label>
                                                        <textarea id="addMessage" name="message" class="form-control" rows="15"></textarea>
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


                   

                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>MESSAGE</th>
                                        <th>DATE CREATED</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>MESSAGE</th>
                                        <th>DATE CREATED</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($announcements as $index => $announcement)
                                        <tr>
                                            <td>{{ $announcement->title }}</td>
                                            <td>
                                                {{ \Illuminate\Support\Str::limit(
                                                    str_replace(["\r\n", "\r", "\n", "\\r\\n", "\\n", "\\r"], ' ', $announcement->message),
                                                    50,
                                                    '...',
                                                ) }}
                                            </td>
                                            <td>{{ $announcement->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-sm btn-primary"
                                                        onclick="openEditModal({{ $announcement->id }}, '{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->message) }}')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <form id="delete-form-{{ $announcement->id }}"
                                                        action="{{ route('admin.announcement.destroy', $announcement->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="{{ $announcement->id }}">
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
            const form = document.getElementById('AnnouncementForm');
            form.action = '{{ route('admin.announcement.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('addTitle').value = '';
            document.getElementById('addMessage').value = '';
            document.getElementById('modalTitle').textContent = 'Add Announcement';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        function openEditModal(id, title, message) {
            const form = document.getElementById('AnnouncementForm');
            form.action = `/admin/announcement/${id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('addTitle').value = title;
            const converted = message.replace(/\\n/g, '\n');
            document.getElementById('addMessage').value = converted;

            document.getElementById('modalTitle').textContent = 'Edit Announcement';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        // Reset modal when closed
        $('#addRowModal').on('hidden.bs.modal', function() {
            const form = document.getElementById('AnnouncementForm');
            form.reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').textContent = 'Add Announcement';
        });

        // Disable submit to prevent multiple clicks
        $('#AnnouncementForm').on('submit', function() {
            const btn = $('#addRowButton');
            btn.prop('disabled', true).text('Saving');
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let announcementId = $(this).data('id');

            swal({
                title: "Are you sure you want to delete this announcement?",
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
                    document.getElementById(`delete-form-${announcementId}`).submit();
                }
            });
        });
    </script>
@endpush
