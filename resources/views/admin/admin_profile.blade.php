@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">

        <div class="page-header">
            <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">Home</li>
     <li class="breadcrumb-item active" aria-current="page">Manage</li>
      <li class="breadcrumb-item active" aria-current="page">Profile</li>
  </ol>
</nav>
        </div>

        <div class="row">
          <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Manage User</div>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" id="exampleValidation">
            @csrf

            <div class="card-body">
                <!-- Firstname -->
                <div class="form-group form-show-validation row">
                    <label for="firstname" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Firstname</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <input type="text" class="form-control" id="firstname" name="first_name"
                               placeholder="Enter Firstname"
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Lastname -->
                <div class="form-group form-show-validation row">
                    <label for="lastname" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Lastname</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <input type="text" class="form-control" id="lastname" name="last_name"
                               placeholder="Enter Lastname"
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Email read-only -->
                <div class="form-group form-show-validation row">
                    <label for="email" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Email Address</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter Email"
                               value="{{ $user->email }}" readonly>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group form-show-validation row">
                    <label for="password" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Password</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter Password">
                        <small class="form-text text-muted">
                            Leave this blank if you don’t want to change the password.
                        </small>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group form-show-validation row">
                    <label for="password_confirmation" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Confirm Password</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <input type="password" class="form-control" id="password_confirmation"
                               name="password_confirmation" placeholder="Enter Confirm Password">
                        @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="separator-solid"></div>

                <!-- Upload Image -->
                <div class="form-group form-show-validation row">
                    <label class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-end">Upload Image</label>
                    <div class="col-lg-4 col-md-9 col-sm-8">
                        <div class="input-file input-file-image">
                            <img id="preview-image"
                                 class="img-upload-preview img-circle mb-2"
                                 width="100" height="100"
                                 @if ($user->avatar)
                                     src="{{ asset('storage/' . $user->avatar) }}"
                                 @else
                                     src="{{ asset('default.png') }}"
                                 @endif
                                 alt="preview"
                            >

                            <input type="file" class="form-control form-control-file"
                                   id="uploadImg" name="uploadImg"
                                   accept=".jpg,.jpeg,.png">

                            <label for="uploadImg" class="btn btn-primary btn-round btn-lg mt-2">
                                <i class="fa fa-file-image"></i> Upload an Image
                            </label>

                            @error('uploadImg')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-action">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{ route('admin.archive') }}" class="btn btn-danger">Cancel</a>
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
        @if (session('success'))
            swal({
                title: "Success!",
                text: "{{ session('success') }}",
                icon: "success",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-success"
                    }
                }
            });
        @endif

        @if (session('error'))
            swal({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                buttons: {
                    confirm: {
                        text: "Try Again",
                        className: "btn btn-danger"
                    }
                }
            });
        @endif
    </script>


    <script>
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
                uploadImg: {
                    extension: "jpg|jpeg|png" // ✅ instead of "required", only allow specific file types
                }
            },
            messages: {
                gender: "Please select a gender",
                confirmpassword: "Passwords do not match",
                birth: "Please enter a valid date",
                uploadImg: "Only JPG, JPEG, or PNG files are allowed"
            },
            highlight: function(element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            success: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });
    </script>
@endpush
