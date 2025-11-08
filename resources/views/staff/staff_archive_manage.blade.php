@php
    $role = auth()->check() ? auth()->user()->role : 'patron';
    if (!in_array($role, ['admin', 'staff'])) {
        $role = 'patron';
    }
@endphp

@extends('staff.staff_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
    <li class="breadcrumb-item active" aria-current="page">Archive</li>
    <li class="breadcrumb-item active" aria-current="page">Manage</li>
  </ol>
</nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Manage Archive</h4>
                            <a href="{{ route('staff.manage.archive.page') }}" class="btn btn-primary btn-round ms-auto">
                                <i class="fa fa-plus"></i>
                                New Archive
                            </a>
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
                                                        <select class="form-select form-control" id="program"
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
                                                        <label for="subject">Subject</label>
                                                        <textarea class="form-control" id="subject" name="subject" rows="3" required></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="authors">Authors</label>
                                                        <textarea class="form-control" id="authors" name="authors" rows="3" required placeholder="Juan Delacruz"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="file_path">Upload PDF File</label>
                                                        <div id="currentFile" class="mb-2"></div>

                                                        <input type="file" class="form-control-file" id="file_path"
                                                            name="file_path" accept="application/pdf" />

                                                    </div>
                                                </div>






                                                {{-- <div class="col-sm-7">
                                                    <div class="form-group">
                                                        <label for="citation">Recommend Citation</label>
                                                        <textarea class="form-control" id="citation" name="citation" rows="3"></textarea>
                                                    </div>
                                                </div> --}}







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
                                       
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>CATEGORY</th>
                                        <th>CREATED</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                               
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr>
                                            <td>{{ $archive->archive_code }}</td>
                                           <td class="">{{ $archive->title }}</td>
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
                                                        <a href="{{ route($role . '.archive.details', $archive->id) }}"
                                                            class="btn btn-sm btn-info" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>


                                                   

                                        

                                                    <!-- Edit Button -->
                                                        <a href="{{ route($role . '.archive.manage.edit', $archive->id) }}"
                                                            class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>


                                                    <!-- Delete Button -->
                                                    <form id="delete-form-{{ $archive->id }}"
                                                            action="{{ route($role . '.archive.manage.destroy', $archive->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                                title="Delete" data-id="{{ $archive->id }}">
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
            swal("Good job!", "{{ session('success') }}", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: 'btn btn-danger'
                    }
                },
            });
        @endif


        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let archiveId = $(this).data('id');


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
                    document.getElementById(`delete-form-${archiveId}`).submit();
                }
            });
        });
    </script>
@endpush
