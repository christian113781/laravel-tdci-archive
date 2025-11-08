@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">

        <div class="page-header">
            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
     <li class="breadcrumb-item active" aria-current="page">Manage</li>
      <li class="breadcrumb-item active" aria-current="page">User Role</li>
  </ol>
</nav>

        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">User Role</h4>
                            <a href="{{ route('admin.manage') }}" class="btn btn-primary btn-round ms-auto">
                                <i class="fa fa-plus"></i> Add New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">


                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @if ($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" width="40"
                                                        height="40" class="rounded-circle">
                                                @else
                                                    <img src="" class="rounded-circle">
                                                @endif
                                            </td>
                                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ ucfirst($user->role) }}</td>
                                            <td>
                                                <div
                                                    class="form-button-action d-flex gap-2 justify-content-center align-items-center">
                                                    <!-- Edit Button -->
                                                    <a href="{{ route('admin.manage.fetchID', $user->id) }} "
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit fs-6"></i>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                        data-id="{{ $user->id }}" data-name="{{ $user->last_name }}"
                                                        data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                                    <!-- Hidden Delete Form -->
                                                    <form id="delete-form-{{ $user->id }}"
                                                        action="{{ route('admin.user.destroy', $user->id) }}"
                                                        method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
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


        $(document).ready(function() {
    $(document).on('click', '.btn-view-user', function() {
        const id = $(this).data('id');
        alert('Clicked! User ID: ' + id);
        console.log('Clicked! User ID:', id);
    });
});

       
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






        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            let userId = $(this).data('id');
            let userLastName = $(this).data('name');

            console.log('User ID:', userId);
            console.log('Last Name:', userLastName);

            swal({
                title: `Delete ${userLastName}?`,
                text: "This action cannot be undone!",
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
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            });
        });
    </script>
@endpush
