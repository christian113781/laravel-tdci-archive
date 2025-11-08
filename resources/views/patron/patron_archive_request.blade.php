@extends('patron.patron_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Archive</li>
                    <li class="breadcrumb-item active" aria-current="page">Request</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Archive Request</h4>
                    </div>


                    <div class="card-body">



                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>AUTHOR</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>CATEGORY</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($archives as $archive)
                                        <tr>
                                            <td style="text-align: justify;">{{ $archive->title }}</td>
                                            <td>{{ $archive->authors }}</td>
                                            <td>{{ $archive->year }}</td>
                                            <td>{{ $archive->program->name ?? 'N/A' }}</td>


                                            <td>
                                                @if ($archive->category === 'A')
                                                    <span class="badge bg-info fs-7 px-2 py-2">General</span>
                                                @elseif ($archive->category === 'B')
                                                    <span class="badge bg-danger fs-7 px-2 py-2">Limited Access</span>
                                                @else
                                                    <span class="badge bg-secondary fs-7 px-3 py-2">N/A</span>
                                                @endif
                                            </td>


                                            <td>
                                                @php
                                                    $request = $archive->accessRequests->first();
                                                    $status = $request ? $request->status : null;
                                                @endphp

                                                @if ($status === 'pending')
                                                    <span class="badge bg-warning fs-7 px-2 py-2">Pending</span>
                                                @elseif ($status === 'approved')
                                                    <span class="badge bg-success fs-7 px-2 py-2">Approved</span>
                                                @elseif ($status === 'rejected')
                                                    <span class="badge bg-danger fs-7 px-2 py-2">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary fs-7 px-2 py-2">No Request</span>
                                                @endif
                                            </td>

                                            <td>
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
            $('#basic-datatables').DataTable({
                "pageLength": 50,
                "order": [
                    [6, "desc"]
                ]
            });
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

    <script></script>
@endpush
