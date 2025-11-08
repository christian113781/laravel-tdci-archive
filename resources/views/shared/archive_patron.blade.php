@extends('patron.patron_dashboard')

<style>
    .wrap-text {
        white-space: normal;
        word-wrap: break-word;
        min-width: 200px;

    }

    #basic-datatables td {
        vertical-align: middle;
    }
</style>





@section('pages')
    <div class="page-inner ms-lg-0">
        <div class="page-header ">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Archive</li>
                    <li class="breadcrumb-item active" aria-current="page">List</li>
                </ol>
            </nav>
        </div>

        <div class="row">

            <div class="col-md-12">

                <form action="{{ route('patron.archive') }}" method="GET" class="mb-1">
                    <div class="card mb-0 rounded" style="background: transparent !important; box-shadow: none;">

                        <div class="card-body">

                            <div class=" gap-2 justify-content-center align-items-center d-flex flex-column flex-md-row">

                                <!-- Dropdown -->
                                <div class="col-12 col-md-3">
                                    <select name="field" class="form-select form-control w-100">
                                        <option value="">Any field</option>
                                        <option value="1" {{ request('field') == 1 ? 'selected' : '' }}>Title
                                        </option>
                                        <option value="2" {{ request('field') == 2 ? 'selected' : '' }}>Author
                                        </option>
                                        <option value="3" {{ request('field') == 3 ? 'selected' : '' }}>Keyword
                                        </option>
                                        <option value="5" {{ request('field') == 5 ? 'selected' : '' }}>Program
                                        </option>
                                        <option value="4" {{ request('field') == 4 ? 'selected' : '' }}>Year
                                        </option>
                                        <option value="6" {{ request('field') == 6 ? 'selected' : '' }}>Subject
                                        </option>
                                    </select>
                                </div>

                                <!-- Search Input -->
                                <div class="col-12 col-md-4">
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

                    </div>
                </form>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Archives</h4>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Views</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr data-subject="{{ $archive->subject }}" data-id="{{ $archive->id }}"
                                            data-authors="{{ $archive->authors }}" data-year="{{ $archive->year }}"
                                            data-keywords="{{ $archive->keywords->pluck('name')->join(', ') }}">
                                            <td>{{ $archive->title }}</td>
                                            <td>
                                                @if ($archive->category === 'A')
                                                    <span class="badge bg-info">General</span>
                                                @elseif ($archive->category === 'B')
                                                    <span class="badge bg-danger">Limited Access</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td><span class="badge badge-secondary">{{ $archive->views }}</span></td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <button class="btn btn-sm btn-info details-control" title="Details"><i
                                                            class="fa fa-plus-square"></i></button>

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

                                                    <form action="{{ route('patron.archive.toggle', $archive->id) }}"
                                                        method="POST" class="d-inline m-0 p-0">
                                                        <!-- make form inline and remove margins/paddings -->
                                                        @csrf
                                                        @method('PUT')

                                                        <button type="submit"
                                                            class="btn btn-sm {{ $isBookmarked ? 'btn-danger' : 'btn-secondary' }}"
                                                            title="{{ $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark' }}">
                                                            <i class="fa fa-bookmark"></i>
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
            order: [
                [2, 'desc']
            ],
            responsive: true,
            autoWidth: true


        });

        function toggleDetailsForTr($tr) {
            var table = $('#basic-datatables').DataTable();
            var row = table.row($tr);

            // (… your code for closing other rows …)

            if (row.child.isShown()) {
                row.child.hide();
                $tr.removeClass('shown');
                $tr.find('button.details-control i').removeClass('fa-minus-square').addClass('fa-plus-square');
                $tr.find('button.details-control').removeClass('btn-danger').addClass('btn-info');
            } else {
                row.child(formatDetails($tr)).show();
                $tr.addClass('shown');
                $tr.find('button.details-control i').removeClass('fa-plus-square').addClass('fa-minus-square');
                $tr.find('button.details-control').removeClass('btn-info').addClass('btn-danger');
            }
        }

        // button click -> use toggle function (stop propagation so td handler won't run)
        $('#basic-datatables tbody').on('click', 'button.details-control', function(e) {
            e.stopPropagation();
            var $tr = $(this).closest('tr');
            toggleDetailsForTr($tr);
        });

        // first column click -> use same toggle function (no trigger('click') or duplicate logic)
        $('#basic-datatables tbody').on('click', 'td:first-child', function(e) {
            toggleDetailsForTr($(this).closest('tr'));
        });

        function formatDetails($tr) {
            // $tr is the jQuery object for the <tr>
            var authors = $tr.data('authors') || '';
            var published = $tr.data('year') || '';
            var keywords = $tr.data('keywords') || '';
            var subject = $tr.data('subject') || '';

            var kwBadges = keywords.split(',').map(function(k) {
                return k.trim() ;
            }).join('');

            return `
  <div class="border-0 shadow-none">
    <div class="row align-items-center  text-muted">

      <div class="col-md-12 mb-4">
        <div class="d-flex align-items-center">
          <i class="fas fa-user-alt me-2"></i>
          by:&nbsp
          <span>${authors}</span>
          &nbsp; | &nbsp;
          <i class="fas fa-calendar  me-2"></i>
          <strong></strong>
          <span class="ms-2">${published}</span>
        </div>
      </div>

      <div class="col-md-12 mb-2">
        <div class="alert alert-warning" style=" box-shadow: none !important" >
        ${subject}
        </div>
      </div>
      
      <div class="col-md-12">
        <div class="d-flex align-items-center flex-wrap">
          <i class="fas fa-tags  me-2"></i>
          <strong> </strong>
          <div class="ms-2"><i class="text-sm" style="font-size: 0.9em;">${kwBadges}</i></div>
        </div>
      </div>

    </div>
  </div>
`;

        }

        $('#basic-datatables tbody').on('click', '.view-all-details', function(e) {
            e.stopPropagation(); // if you want to prevent row toggling etc.

            var archiveId = $(this).data('id');

            if (!archiveId) {
                console.error('Archive ID not found');
                return;
            }
            var url = "{{ route('patron.archive.details', ['id' => ':id']) }}";
            url = url.replace(':id', archiveId);


            window.location.href = url;
        });
    </script>
    <script>
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
                        text: "You must request access to view this archive.",
                        icon: "info",
                        buttons: {
                            cancel: {
                                text: "Cancel",
                                visible: true,
                                className: "btn btn-light"
                            },
                            confirm: {
                                text: "Request Access",
                                className: "btn btn-secondary"
                            }
                        },
                    }).then((willRequest) => {
                        if (willRequest) {

                            document.getElementById('request-access-form-' + archiveId)
                                .submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
