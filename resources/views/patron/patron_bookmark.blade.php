@extends('patron.patron_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
            <div class="page-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">Bookmarks</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bookmark</h4>
                    </div>
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
                                        <th>YEAR</th>
                                    
                                        <th>CATEGORY</th>
                                        
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr>
                                            <td style="text-align: justify;">{{ $archive->title }}</td>
                                           
                                            <td>{{ $archive->year }}</td>
                                            
                                            <td>
                                                @if ($archive->category === 'A')
                                                    <span class="badge bg-info fs-7 px-2 py-2">General</span>
                                                @elseif ($archive->category === 'B')
                                                    <span class="badge bg-danger fs-7 px-2 py-2">Limited Access</span>
                                                @else
                                                    <span class="badge bg-secondary fs-7 px-3 py-2">N/A</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center ">

                                                    @php
                                                        $canView = $archive->userCanView(auth()->id());
                                                    @endphp

                                                    @if ($canView)
                                                        <a href="{{ route('patron.archive.details', $archive->id) }}"
                                                            class="btn btn-sm ms-2 btn-primary btn-view" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @else
                                                        <form id="request-access-form-{{ $archive->id }}"
                                                            action="{{ route('patron.archive.requestAccess', $archive->id) }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                        </form>

                                                        <button type="button"
                                                            class="btn btn-sm ms-2 btn-primary btn-view-blocked"
                                                            data-archive-id="{{ $archive->id }}" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif

                                                    @php
                                                        $isBookmarked =
                                                            $archive->bookmarks->where('user_id', Auth::id())->count() >
                                                            0;
                                                    @endphp

                                                    <form action="{{ route('patron.bookmark.toggle', $archive->id) }}"
                                                        method="POST" class="d-inline bookmarkForm">
                                                        @csrf
                                                        @method('PUT')

                                                        <button type="submit"
                                                            class="btn btn-sm {{ $isBookmarked ? 'btn-danger' : 'btn-secondary' }}"
                                                            title="{{ $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark' }}">
                                                            <i
                                                                class="fa {{ $isBookmarked ? 'fas fas fa-bookmark' : 'fas fa-bookmark' }}"></i>
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

     $('#basic-datatables').DataTable({

            columnDefs: [{
                targets: 0, // First column
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<div class="wrap-text">' + data + '</div>';
                    }
                    return data;
                }
            }, ],
            responsive: true,
            autoWidth: true

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

            let keywordId = $(this).data('id');

            swal({
                title: "Are you sure you want to delete this keyword?",
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
                    document.getElementById(`delete-form-${keywordId}`).submit();
                }
            });
        });

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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-view-blocked').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const archiveId = this.getAttribute('data-archive-id');

                    swal({
                        title: "Access Required",
                        text: "You already request access to view this archive.",
                        icon: "info",
                        buttons: {
                            confirm: {
                                text: "Cancel",
                                className: "btn btn-secondary"
                            }
                        },
                    })
                });
            });
        });
    </script>
@endpush
