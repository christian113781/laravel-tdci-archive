@extends('patron.patron_dashboard')

@section('archive-request')
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
                    <a href="#">Mange Request</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">


                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>ABSTRACT</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>KEYWORD</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>TITLE</th>
                                        <th>ABSTRACT</th>
                                        <th>YEAR</th>
                                        <th>PROGRAM</th>
                                        <th>KEYWORD</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($requests as $req)
                                        <tr>
                                            <td style="text-align: justify;">{{ Str::limit($req->archive->title, 200) }}
                                            </td>
                                            <td style="text-align: justify;">{{ Str::limit($req->archive->abstract, 200) }}
                                            </td>
                                            <td>{{ $req->archive->year }}</td>
                                            <td>{{ $req->archive->program->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($req->archive->keywords && $req->archive->keywords->isNotEmpty())
                                                    {{ $req->archive->keywords->pluck('name')->join(', ') }}
                                                @else
                                                    <span class="text-muted">No keywords</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif ($req->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif ($req->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">Unknown</span>
                                                @endif
                                            </td>


                                            <td class="text-center">
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center p-2">
                                                    <form method="POST"
                                                        action="{{ route('patron.archive.request.destroy', $req->id) }}"
                                                        class="delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-delete"
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
        // Initialize DataTables
        $('#basic-datatables').DataTable();
    });

    // Delete confirmation using SweetAlert
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let $btn = $(this);
        let form = $btn.closest('form');

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: {
                cancel: {
                    visible: true,
                    text: 'No, cancel!',
                    className: 'btn btn-danger'
                },
                confirm: {
                    text: 'Yes, delete it!',
                    className: 'btn btn-success'
                }
            }
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            } else {
                swal("Your data is safe!", {
                    icon: "info",
                    buttons: {
                        confirm: {
                            className: 'btn btn-primary'
                        }
                    }
                });
            }
        });
    });

    // Display session success or error messages
    @if (session('success'))
        swal("Success!", "{{ session('success') }}", {
            icon: "success",
            buttons: {
                confirm: {
                    className: 'btn btn-success'
                }
            }
        });
    @endif

    @if (session('error'))
        swal("Error!", "{{ session('error') }}", {
            icon: "error",
            buttons: {
                confirm: {
                    className: 'btn btn-danger'
                }
            }
        });
    @endif
</script>
@endpush
