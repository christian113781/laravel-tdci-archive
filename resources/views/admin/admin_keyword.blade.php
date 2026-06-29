@extends('admin.admin_dashboard')

@section('pages')
<div class="page-inner">

    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Manage</li>
                <li class="breadcrumb-item active" aria-current="page">Keywords</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Keywords</h4>
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
                                        <span class="fw-mediumbold">New Keyword</span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="keywordForm" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="_method" id="formMethod"
                                            value="{{ old('_method', 'POST') }}">
                                        <input type="hidden" name="keyword_id" id="keywordId"
                                            value="{{ old('keyword_id') }}">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group form-group-default">
                                                    <label for="addName">Keyword</label>
                                                    <input id="addName" name="name" type="text" class="form-control"
                                                        placeholder="Enter Keyword Name" value="{{ old('name') }}"
                                                        required>
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
                                @foreach ($keywords as $index => $keyword)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $keyword->name }}</td>
                                    <td>{{ $keyword->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div
                                            class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                            <!-- Edit Button -->
                                            <button class="btn btn-sm btn-primary"
                                                onclick="openEditModal({{ $keyword->id }}, '{{ addslashes($keyword->name) }}')">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <form id="delete-form-{{ $keyword->id }}"
                                                action="{{ route('admin.keyword.destroy', $keyword->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $keyword->id }}"
                                                    data-keyword-name="{{ $keyword->name }}"
                                                    data-archives-count="{{ $keyword->archives->count() }}">
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

            @if (session('error'))
                swal("Deleted", "{{ session('error') }}", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: 'btn btn-danger'
                        }
                    },
                });
            @endif

            @if ($errors->any())
                swal("Error!", @json($errors->first('name')), {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: 'btn btn-danger'
                        }
                    },
                });

                @php $oldMethod = old('_method', 'POST'); @endphp
                @if ($oldMethod === 'PUT')
                    let oldKeywordId = @json(old('keyword_id'));
                    document.getElementById('keywordForm').action = `/admin/keyword/${oldKeywordId}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('keywordId').value = oldKeywordId;
                    document.getElementById('addName').value = @json(old('name'));
                    document.getElementById('modalTitle').textContent = 'Edit Keyword';
                    new bootstrap.Modal(document.getElementById('addRowModal')).show();
                @else
                    document.getElementById('keywordForm').action = '{{ route('admin.keyword.store') }}';
                    document.getElementById('formMethod').value = 'POST';
                    document.getElementById('keywordId').value = '';
                    document.getElementById('addName').value = @json(old('name'));
                    document.getElementById('modalTitle').textContent = 'Add Keyword';
                    new bootstrap.Modal(document.getElementById('addRowModal')).show();
                @endif
            @endif
        });

        function openAddModal() {
            document.getElementById('keywordForm').action = '{{ route('admin.keyword.store') }}';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('addName').value = '';
            document.getElementById('modalTitle').textContent = 'Add Keyword';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        function openEditModal(id, name) {
            document.getElementById('keywordForm').action = `/admin/keyword/${id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('keywordId').value = id;
            document.getElementById('addName').value = name;
            document.getElementById('modalTitle').textContent = 'Edit Keyword';
            new bootstrap.Modal(document.getElementById('addRowModal')).show();
        }

        $('#addRowModal').on('hidden.bs.modal', function() {
            document.getElementById('keywordForm').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').textContent = 'Add Keyword';
        });

        // Disable submit to prevent multiple clicks
        $('#keywordForm').on('submit', function() {
            const btn = $('#addRowButton');
            btn.prop('disabled', true).text('Saving');
        });


        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let keywordId = $(this).data('id');
            let keywordName = $(this).data('keyword-name');
            let archivesCount = $(this).data('archives-count');

            let message =
                `This keyword "${keywordName}" is assigned to <strong>${archivesCount} archive${archivesCount !== 1 ? 's' : ''}</strong>.`;
            if (archivesCount > 0) {
                message += ` Deleting it will affect these archives.`;
            }
            message += ` Are you sure you want to delete this keyword?`;

            swal({
                title: "Delete Keyword?",
                content: {
                    element: 'div',
                    attributes: {
                        innerHTML: message
                    }
                },
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "btn btn-secondary"
                    },
                    confirm: {
                        text: "Yes, delete!",
                        className: "btn btn-danger"
                    }
                }
            }).then((willDelete) => {
                if (willDelete) {
                    document.getElementById(`delete-form-${keywordId}`).submit();
                }
            });
        });
</script>
@endpush