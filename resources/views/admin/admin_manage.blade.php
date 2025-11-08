@php
    $isEdit = isset($user);
@endphp


@php
    $hasAvatar = isset($user) && !empty($user->avatar);
    $avatarUrl = $hasAvatar
        ? asset('storage/' . $user->avatar) // only add 'storage/', not 'avatars/...'
        : 'https://via.placeholder.com/100';
@endphp

@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">

        <div class="page-header">
						<h3 class="fw-bold mb-3">DataTables.Net</h3>
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
								<a href="#">Datatables</a>
							</li>
						</ul>
					</div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Manage User</div>
                    </div>
                    <form method="POST"
                        action="{{ $user->exists ? route('admin.manage.update', $user->id) : route('admin_manage_store') }}"
                        enctype="multipart/form-data" id="exampleValidation">
                        @csrf
                        @if ($user->exists)
                            @method('PUT')
                        @endif
                        <div class="card-body">
                            <div class="form-group form-show-validation row">
                                <label for="firstname" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Firstname</label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                        placeholder="Enter Firstname"
                                        value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                </div>
                            </div>

                            <div class="form-group form-show-validation row">
                                <label for="lastname" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Lastname </label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                        placeholder="Enter Lastname" value="{{ old('last_name', $user->last_name ?? '') }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-group form-show-validation row">
                                <label for="email" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Email
                                    Address</label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Enter Email" value="{{ old('email', $user->email ?? '') }}"
                                        {{ $user->exists ? 'readonly' : '' }} required>
                                </div>
                            </div>
                            <div class="form-group form-show-validation row">
                                <label for="password" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Password</label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter Password" @required(!$user->exists)>

                                    @if ($user->exists)
                                        <small id="passhelp" class="form-text text-muted">
                                            Leave this blank if you don't want to change the password.
                                        </small>
                                    @endif
                                </div>
                            </div>
                            @unless ($user->exists)
                                <div class="form-group form-show-validation row">
                                    <label for="confirmpassword" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Confirm
                                        Password</label>
                                    <div class="col-lg-4 col-md-9 col-sm-8">
                                        <input type="password" class="form-control" id="confirmpassword" name="confirmpassword"
                                            placeholder="Enter Confirm Password">
                                    </div>
                                </div>
                            @endunless


                            <div class="form-group form-show-validation row">
                                <label for="usertype" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">User Type</label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <select class="form-select form-control" id="usertype" name="usertype" required>
                                        <option value="Admin"
                                            {{ strtolower(old('usertype', $user->role ?? '')) === 'admin' ? 'selected' : '' }}>
                                            Admin</option>
                                        <option value="Staff"
                                            {{ strtolower(old('usertype', $user->role ?? '')) === 'staff' ? 'selected' : '' }}>
                                            Staff</option>
                                    </select>
                                </div>
                            </div>


                            <div class="separator-solid"></div>

                            <div class="form-group form-show-validation row">
                                <label class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">
                                    Upload Image
                                </label>
                                <div class="col-lg-4 col-md-9 col-sm-8">
                                    <div class="input-file input-file-image">
                                        <img id="preview-image" class="img-upload-preview img-circle mb-2" width="100"
                                            height="100" src="{{ $avatarUrl }}" alt="preview">

                                        <input type="file" class="form-control form-control-file" id="uploadImg"
                                            name="uploadImg" accept="image/*">

                                        <label for="uploadImg" class="btn btn-primary btn-round btn-lg mt-2">
                                            <i class="fa fa-file-image"></i> Upload an Image
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-action">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary" type="submit">Save</button>
                                    <a href="{{ route('admin.user') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#alert_demo_8').click(function(e) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
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
                    swal("Poof! Your imaginary file has been deleted!", {
                        icon: "success",
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        }
                    });
                } else {
                    swal("Your imaginary file is safe!", {
                        buttons: {
                            confirm: {
                                className: 'btn btn-success'
                            }
                        }
                    });
                }
            });
        })


        $("#exampleValidation").validate({
            validClass: "success",
            rules: {
                gender: {
                    required: true
                },
                confirmpassword: {
                    equalTo: "#password"
                },
                birth: {
                    date: true
                },
                // uploadImg: {
                //     required: true,
                // },
            },
            highlight: function(element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            success: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
        });
    </script>
@endpush
