@extends('admin.admin_dashboard')

@section('pages')
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
                    <a href="#">Tables</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Archive List</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <form method="GET" action="{{ route('admin.archive') }}">
                        <div class="card-header">
                            <div
                                class="row gx-0 gx-md-2 gy-2 justify-content-center align-items-center d-flex flex-column flex-md-row">

                                <!-- Dropdown -->
                                <div class="col-12 col-md-2">
                                    <select name="field" class="form-select form-control w-100">
                                        <option value="">Any field</option>
                                        <option value="1" {{ request('field') == 1 ? 'selected' : '' }}>Title</option>
                                        <option value="2" {{ request('field') == 2 ? 'selected' : '' }}>Author</option>
                                        <option value="3" {{ request('field') == 3 ? 'selected' : '' }}>Keyword
                                        </option>
                                        <option value="4" {{ request('field') == 4 ? 'selected' : '' }}>Year</option>
                                        <option value="5" {{ request('field') == 5 ? 'selected' : '' }}>Program</option>
                                        <option value="6" {{ request('field') == 6 ? 'selected' : '' }}>Subject
                                        </option>
                                    </select>
                                </div>

                                <!-- Search Input -->
                                <div class="col-12 col-md-2">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control w-100" placeholder="Search...">
                                </div>

                                <!-- Search Button -->
                                <div class="col-12 col-md-auto text-center">
                                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card-body">
                        <!-- View Archive Modal -->
                        <div class="modal fade" id="viewArchive" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <!-- modal-xl for extra large -->
                                <div class="modal-content" style="max-width: 1200px; margin: auto;">
                                    <!-- increase max-width -->
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title">Thesis Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div id="archiveDetails" class="mb-3"></div>
                                        <div id="pdfViewerContainer"></div>
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


                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>SUBJECT</th>
                                        <th>AUTHOR</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>KEYWORD</th>
                                        <th>VIEWS</th>
                                        <th>CATEGORY</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>SUBJECT</th>
                                        <th>AUTHOR</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>KEYWORD</th>
                                        <th>VIEWS</th>
                                        <th>CATEGORY</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr>
                                            <td style="text-align: justify;">{{ Str::limit($archive->title, 120) }}</td>
                                            <td style="text-align: justify;">{{ Str::limit($archive->subject, 120) }}</td>
                                            <td>{{ $archive->authors }}</td>
                                            <td>{{ $archive->year }}</td>
                                            <td>{{ $archive->program->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($archive->keywords->isNotEmpty())
                                                    {{ $archive->keywords->pluck('name')->join(', ') }}
                                                @else
                                                    <span class="text-muted">No keywords</span>
                                                @endif
                                            </td>
                                            <td> <span
                                                    class="badge bg-secondary fs-7 px-2 py-2">{{ $archive->views }}</span>
                                            </td>
                                            <td>
                                                @if ($archive->category === 'A')
                                                    <span class="badge bg-info fs-7 px-2 py-2">GENERAL</span>
                                                @elseif ($archive->category === 'B')
                                                    <span class="badge bg-danger fs-7 px-2 py-2">RESTRICTED</span>
                                                @else
                                                    <span class="badge bg-secondary fs-7 px-3 py-2">N/A</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center ">

                                                    <a href="#" class="btn btn-sm btn-info view-archive"
                                                        title="View" data-id="{{ $archive->id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

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
        $('#dateyear').datetimepicker({
            format: 'YYYY',
            viewMode: 'years'
        }).on('dp.show', function(e) {
            // force focus back to years panel
            var picker = $(this).data("DateTimePicker");
            picker.viewMode("years");
        });


        $(document).ready(function() {
            $('#basic-datatables').DataTable({
                "pageLength": 50,
                "order": [
                    [6, "desc"]
                ] // sort column 6 (views) descending
            });
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
            let url = "{{ url('admin/archives') }}/" + archiveId + "/view";

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    let pdfViewer = '';
                    let requestButton = '';

                    // Determine if user can view PDF
                    const canView = data.category === 'A' || data.request_status === 'approved';

                    if (canView) {
                        if (/Mobi|Android/i.test(navigator.userAgent)) {
                            pdfViewer = `
                        <div class="text-center my-3">
                            <p class="mb-2">PDF preview is not supported on mobile.</p>
                            <a href="${data.file_path}" class="btn btn-primary" target="_blank">
                                Open PDF
                            </a>
                        </div>`;
                        } else {
                            pdfViewer = `
                        <div class="ratio ratio-16x9 mt-3">
                            <iframe src="${data.file_path}" style="border:0;" allowfullscreen></iframe>
                        </div>`;
                        }
                        $('#downloadPdf').show().attr('href', data.file_path);
                    } else {
                        // For restricted archives without approval
                        pdfViewer = `
                    <div class="text-center my-3">
                        <p class="text-danger fw-bold">You do not have access to view this PDF.</p>
                    </div>`;
                        $('#downloadPdf').hide();

                        // Request Access button
                        requestButton = `
                    <div class="text-center my-3">
                        <a href="#" class="btn btn-warning requestAccessBtn" data-id="${data.id}">
                            <i class="fas fa-envelope me-2"></i> Request Access
                        </a>
                    </div>`;
                    }

                    // Modal content
                    let content = `
<div class="mb-4 px-3">
    <h4 class="fw-bold mb-3 text-center">${data.title}</h4>

     ${data.subject ? `
            <div class="mb-3 d-flex justify-content-between align-items-start">
                <p class="mb-0"><strong>Subject:</strong></p>
                ${data.year ? `<p class="mb-0"><strong>Year:</strong> ${data.year}</p>` : ''}
            </div>
            <p style="text-align: justify;">${data.subject}</p>
        ` : ''}

    <div class="row mb-3">
        ${data.program ? `
                <div class="col-md-6 col-12 mb-2">
                    <p class="mb-0"><strong>Program:</strong> ${data.program}</p>
                </div>` : ''}
        ${data.authors ? `
                <div class="col-12 mb-2">
                    <p class="mb-0"><strong>Authors:</strong> ${data.authors}</p>
                </div>` : ''}
        <div class="col-12">
            <p class="mb-0"><strong>Category:</strong> ${data.category === 'A' ? 'GENERAL' : 'RESTRICTED'}</p>
        </div>
    </div>

    <div class="mb-3">
        ${pdfViewer}
    </div>

    <div class="text-center">
        ${requestButton}
    </div>
</div>
`;

                    $('#archiveDetails').html(content);
                    $('#viewArchive').modal('show');
                },
                error: function() {
                    alert('Archive not found.');
                }
            });
        });

        // Handle Request Access
        $(document).on('click', '.requestAccessBtn', function(e) {
            e.preventDefault();
            let archiveId = $(this).data('id');

            $.ajax({
                url: "/admin/archive/request-access/" + archiveId, // pass archiveId in URL
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    // SweetAlert for success
                    swal("Success!", response.message, {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        }
                    });

                    // Disable the button after request
                    $('.requestAccessBtn[data-id="' + archiveId + '"]')
                        .prop('disabled', true)
                        .removeClass('btn-warning')
                        .addClass('btn-secondary')
                        .text('Request Sent');
                },
                error: function(xhr) {
                    // Use danger/red for errors
                    swal("Oops!", xhr.responseJSON?.message || 'Something went wrong.', {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: 'btn btn-danger'
                            }
                        }
                    });

                    // Optionally disable the button
                    $('.requestAccessBtn[data-id="' + archiveId + '"]')
                        .prop('disabled', true)
                        .removeClass('btn-warning')
                        .addClass('btn-secondary')
                        .text('Already Requested');
                }
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            @if (isset($filteredCount))
                var placementFrom = "top"; // or bottom
                var placementAlign = "right"; // or left, center
                var state = "{{ request()->has('search') ? 'warning' : 'warning' }}";
                var style = "withicon"; // withicon | withouticon

                var content = {};
                @if (request()->has('search'))
                    content.message = "{{ $filteredCount }} archives found for your search.";
                    content.title = "Filter Results";
                @else
                    content.message = "Showing {{ $archives->total() }} archives.";
                    content.title = "Archive List";
                @endif

                if (style === "withicon") {
                    content.icon = 'fa fa-bell'; // or any FontAwesome icon
                } else {
                    content.icon = 'none';
                }

                // Optional link if you want
                content.url = '#';
                content.target = '_blank';

                $.notify(content, {
                    type: state,
                    placement: {
                        from: placementFrom,
                        align: placementAlign
                    },
                    time: 1000,
                    delay: 2000,
                });
            @endif
        });
    </script>
@endpush
