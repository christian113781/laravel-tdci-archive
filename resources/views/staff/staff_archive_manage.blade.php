@extends('staff.staff_dashboard')

@section('archive')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Archives</h3>
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
                    <a href="#">Manage</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Mange Archive</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Manage Archive</h4>
                            <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                                data-bs-target="#addRowModal">
                                <i class="fa fa-plus"></i>
                                Add New Archive
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Modal -->
                        <div class="modal fade" id="addRowModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title">
                                            <span class="fw-mediumbold">Manage Thesis</span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('staff.archive.manage.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">

                                                <!-- Hidden status field to default as unpublish -->
                                                <input type="hidden" name="status" value="unpublish">

                                                <div class="col-md-3">
                                                    <div class="form-group form-show-validation">
                                                        <label for="archive_code">Archive Code</label>
                                                        <input type="text" class="form-control" id="archive_code"
                                                            name="archive_code" value="{{ $archiveCode }}" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="program">Program</label>
                                                        <select class="form-select form-control" id="program" required
                                                            name="program_id">
                                                            @forelse ($programs as $dept)
                                                                <option value="{{ $dept->id }}"
                                                                    {{ $loop->first ? 'selected' : '' }}>
                                                                    {{ $dept->name }}
                                                                </option>
                                                            @empty
                                                                <option value="">Select Program</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="category">Category</label>
                                                        <select name="category" class="form-select form-control"
                                                            id="category" required>
                                                            <option value="A"
                                                                {{ old('category', $archive->category ?? '') === 'A' ? 'selected' : '' }}>
                                                                A. General</option>
                                                            <option value="B"
                                                                {{ old('category', $archive->category ?? '') === 'B' ? 'selected' : '' }}>
                                                                B. Limited Access</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group form-show-validation">
                                                        <label for="title">Project Title</label>
                                                        <input type="text" class="form-control" id="title"
                                                            name="title" placeholder="" required />
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="multiple">Keyword:</label>

                                                        <div class="select2-input select2-danger">
                                                            <select id="multiple" name="multiple[]"
                                                                class="form-control required" multiple="multiple" required>
                                                                @forelse ($keywords as $keyword)
                                                                    <option value="{{ $keyword->id }}"
                                                                        {{ in_array($keyword->id, old('multiple', [])) ? 'selected' : '' }}>
                                                                        {{ $keyword->name }}
                                                                    </option>
                                                                @empty
                                                                    <option value="" disabled>No keywords list
                                                                    </option>
                                                                @endforelse
                                                            </select>
                                                        </div>

                                                    </div>
                                                </div>





                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Year</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="dateyear"
                                                                name="year" value="{{ now()->year }}" required>
                                                            <span class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="abstract">Abstract</label>
                                                        <textarea class="form-control" id="abstract" name="abstract" rows="5" required></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="authors">Authors</label>
                                                        <textarea class="form-control" id="authors" name="authors" rows="5" required
                                                            placeholder="Fernando, Christian"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="file_path">Upload PDF File</label>
                                                        <div id="currentFile" class="mb-2"></div>

                                                        <input type="file" class="form-control-file" id="file_path"
                                                            name="file_path" accept="application/pdf" />

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer border-0">
                                                <button type="submit" id="addRowButton"
                                                    class="btn btn-primary">Add</button>
                                                <button type="button" class="btn btn-danger"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->


                        <!-- View Archive Modal -->
                        <div class="modal fade" id="viewArchive" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content" style="max-width: 900px; margin: auto;">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title">Thesis Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <!-- Details injected by JS -->
                                        <div id="archiveDetails" class="mb-3"></div>

                                        <!-- PDF Viewer -->
                                        <iframe id="archivePdf" src="" width="100%"
                                            style="border:0; height:65vh;"></iframe>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <a id="downloadPdf" href="#" class="btn btn-primary" download>
                                            <i class="fas fa-download me-2"></i> Download PDF
                                        </a>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i> Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- End Modal -->

                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>TITLE</th>
                                        <th>ABSTRACT</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>CATEGORY</th>
                                        <th>CREATED</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>CODE</th>
                                        <th>TITLE</th>
                                        <th>ABSTRACT</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>CATEGORY</th>
                                        <th>CREATED</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr>
                                            <td>{{ $archive->archive_code }}</td>
                                           <td style="text-align: left;">
    {{ $archive->title }}
</td>
<td style="text-align: justify;">
    {{ Str::limit($archive->abstract, 150) }}
</td>
                                            <td>{{ $archive->year }}</td>
                                            <td>{{ $archive->program->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($archive->category === 'A')
                                                    <span class="badge bg-info">General</span>
                                                @elseif ($archive->category === 'B')
                                                    <span class="badge bg-danger">Limited Access</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $archive->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $archive->status === 'Publish' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $archive->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <!-- Status Toggle -->
                                                    <form
                                                        action="{{ route('staff.archives.manage.updateStatus', $archive->id) }}"
                                                        method="POST" class="d-inline statusForm">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-secondary"
                                                            title="Publish/Unpublish">
                                                            <i
                                                                class="fa {{ strtolower($archive->status) == 'publish' ? 'fas fa-toggle-off' : 'fas fa-toggle-on' }}"></i>
                                                        </button>
                                                    </form>

                                                    <!-- View Button -->
                                                    <a href="#" class="btn btn-sm btn-info view-archive"
                                                        data-id="{{ $archive->id }}" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <!-- Edit Button -->
                                                    <a href="javascript:void(0)"
                                                        class="btn btn-sm btn-primary editArchiveBtn" title="Edit"
                                                        data-id="{{ $archive->id }}">
                                                        <i class="fa fa-edit fs-6"></i>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <form
                                                        action="{{ route('staff.archives.manage.destroy', $archive->id) }}"
                                                        method="POST" class="d-inline deleteForm">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger deleteBtn"
                                                            title="Delete">
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


@push('archive-script')
    <script>
        $(document).ready(function() {
            $('#basic-datatables').DataTable({
                "pageLength": 50,
            });

        });

        $('#dateyear').datetimepicker({
            format: 'YYYY',
            viewMode: 'years'
        }).on('dp.show', function(e) {
            // force focus back to years panel
            var picker = $(this).data("DateTimePicker");
            picker.viewMode("years");
        });

        $('#multi-filter-select').DataTable({
            "pageLength": 50,
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    var select = $('<select class="form-select"><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            }
        });
    </script>

    <script>
        $(document).on('click', '.view-archive', function(e) {
            e.preventDefault();

            let archiveId = $(this).data('id');
            let url = "{{ url('staff/archives') }}/" + archiveId;

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    let pdfViewer;

                    // Detect if mobile
                    if (/Mobi|Android/i.test(navigator.userAgent)) {
                        pdfViewer = `
                    <div class="text-center my-3">
                        <p class="mb-2">PDF preview is not supported on mobile.</p>
                        <a href="${data.file_path}" class="btn btn-primary" target="_blank">
                            Open PDF
                        </a>
                    </div>
                `;
                    } else {
                        pdfViewer = `
                    <div class="ratio ratio-16x9 mt-3">
                        <iframe src="${data.file_path}" style="border:0;" allowfullscreen></iframe>
                    </div>
                `;
                    }

                    // Modal content: details on top, PDF below
                    let content = `
                <!-- Thesis Details -->
                <div class="mb-4">
                <h4 class="fw-bold mb-2 text-center">${data.title}</h4>
                ${data.year ? `<p><strong>Year:</strong> ${data.year}</p>` : ''}
                ${data.program ? `<p><strong>Program:</strong> ${data.program}</p>` : ''}
                ${data.abstract ? `<p style="text-align: justify;"><strong>Abstract:</strong><br>${data.abstract}</p>` : ''}
                ${data.authors ? `<p><strong>Authors:</strong> ${data.authors}</p>` : ''}
                </div>

                <!-- PDF Preview -->
                ${pdfViewer}
            `;

                    $('#viewArchive .modal-body').html(content);
                    $('#downloadPdf').attr('href', data.file_path);
                    $('#viewArchive').modal('show');
                },
                error: function() {
                    alert('Archive not found.');
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modalElement = document.getElementById('addRowModal');
            const modal = new bootstrap.Modal(modalElement);
            const form = modalElement.querySelector('form');
            const fileInput = document.getElementById('file_path');
            const currentFileDiv = document.getElementById('currentFile');

            // ✅ Reset form when opening "Add New Archive"
            modalElement.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // the button that opened the modal
                if (!button.classList.contains('editArchiveBtn')) {
                    // Clear form fields
                    form.reset();

                    // Reset archive_code if Laravel generated one
                    const archiveCodeField = document.getElementById('archive_code');
                    if (archiveCodeField && archiveCodeField.hasAttribute('value')) {
                        archiveCodeField.value = archiveCodeField.getAttribute('value');
                    }

                    // Clear Select2 (keywords)
                    const keywordSelect = document.getElementById('multiple');
                    if (keywordSelect) {
                        [...keywordSelect.options].forEach(option => option.selected = false);
                        $('#multiple').val(null).trigger('change');
                    }

                    // Reset file input (since .reset() doesn’t always clear in all browsers)
                    fileInput.value = "";
                    fileInput.setAttribute("required", "required"); // required when adding
                    if (currentFileDiv) currentFileDiv.innerHTML = "";

                    // Reset form action & button
                    form.action = `{{ route('staff.archive.manage.store') }}`;
                    form.querySelector('input[name="_method"]')?.remove();
                    form.querySelector('#addRowButton').textContent = "Add";
                }
            });

            // ✅ Edit button logic
            document.querySelectorAll('.editArchiveBtn').forEach(button => {
                button.addEventListener('click', function() {
                    let id = this.dataset.id;

                    fetch(`/staff/archives/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            // Fill fields
                            document.getElementById('archive_code').value = data.archive_code;
                            document.getElementById('title').value = data.title;
                            document.getElementById('abstract').value = data.abstract;
                            document.getElementById('authors').value = data.authors;
                            document.getElementById('dateyear').value = data.year;
                            document.getElementById('program').value = data.program_id;
                            document.getElementById('category').value = data.category;

                            // ✅ keywords
                            const keywordSelect = document.getElementById('multiple');
                            [...keywordSelect.options].forEach(option => option.selected =
                                false);
                            if (data.keywords) {
                                data.keywords.forEach(keyword => {
                                    let option = keywordSelect.querySelector(
                                        `option[value="${keyword.id}"]`);
                                    if (option) option.selected = true;
                                });
                            }
                            $('#multiple').trigger('change');

                            // ✅ File display
                            fileInput.value = "";
                            fileInput.removeAttribute("required"); // not required when editing
                            if (currentFileDiv) {
                                if (data.file_path) {
                                    currentFileDiv.innerHTML = `
                                    <p>Current File:
                                        <a href="/storage/${data.file_path}" target="_blank">View PDF</a>
                                    </p>`;
                                } else {
                                    currentFileDiv.innerHTML =
                                        `<p class="text-muted">No file uploaded yet.</p>`;
                                }
                            }

                            // ✅ Update form action for Edit
                            form.action = `/staff/archives/${id}`;
                            form.querySelector('input[name="_method"]')?.remove();
                            form.insertAdjacentHTML('beforeend',
                                '<input type="hidden" name="_method" value="PUT">');
                            form.querySelector('#addRowButton').textContent = "Update";

                            modal.show();
                        });
                });
            });
        });
    </script>


    <script>
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                let form = this.closest('form');
                let url = form.action;

                swal({
                    title: 'Are you sure?',
                    text: "This archive will be permanently deleted!",
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Cancel',
                            visible: true,
                            className: 'btn btn-danger'
                        },
                        confirm: {
                            text: 'Yes, delete it!',
                            className: 'btn btn-success'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        fetch(url, {
                                method: "POST",
                                body: new FormData(form)
                            })
                            .then(res => res.ok ? res : Promise.reject(res))
                            .then(() => {
                                swal("Deleted!", "Archive has been deleted.", {
                                    icon: "success",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-success'
                                        }
                                    }
                                }).then(() => location.reload()); // reload table
                            })
                            .catch(() => {
                                swal("Error!", "Failed to delete archive.", {
                                    icon: "error",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    }
                                });
                            });
                    }
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


    <script>
        $('#multiple').select2({
            theme: "bootstrap",
            dropdownParent: $('#addRowModal')
        });
    </script>

@endpush
