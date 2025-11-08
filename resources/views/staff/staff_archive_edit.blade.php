@extends('staff.staff_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="page-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Archive</li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('staff.archives.update', $archive->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Thesis Details</h6>
                        </div>
                        <div class="card-body" >
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-show-validation">
                                        <label for="archive_code">Archive Code</label>
                                        <input type="text" class="form-control input-square" id="archive_code"
                                            name="archive_code" value="{{ old('archive_code', $archive->archive_code) }}"
                                            readonly />
                                    </div>
                                </div>


                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="archive_program">Program</label>
                                        <select class="form-select form-control input-square" id="archive_program"
                                            name="archive_program" required>
                                            <option value="" disabled>— Select a Program —</option>
                                            @forelse ($programs as $dept)
                                                <option value="{{ $dept->id }}"
                                                    {{ old('archive_program', $archive->program_id) == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>— No Programs Available —</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Year</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-square" id="archive_year"
                                                name="archive_year" value="{{ old('archive_year', $archive->year) }}"
                                                required>
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>






                                <div class="col-md-5 ">
                                    <div class="form-group">
                                        <label for="archive_category">Category</label>
                                        <select name="archive_category" class="form-select form-control input-square"
                                            id="archive_category" required>
                                            <option value="A"
                                                {{ old('archive_category', $archive->category) === 'A' ? 'selected' : '' }}>
                                                A. General</option>
                                            <option value="B"
                                                {{ old('archive_category', $archive->category) === 'B' ? 'selected' : '' }}>
                                                B. Limited Access</option>
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Keywords</label>
                                        <div class="select2-input select2-danger">
                                            <select id="multiple" name="multiple[]" class="form-control input-square"
                                                multiple required data-placeholder="— Select a Keyword —">
                                                @forelse ($keywords as $keyword)
                                                    <option value="{{ $keyword->id }}"
                                                        {{ in_array($keyword->id, old('multiple', $archive->keywords->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $keyword->name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>— No Keywords Available —</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>





                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="archive_author">Author's</label>
                                        <textarea class="form-control input-square" id="archive_author" name="archive_author" rows="3" required>{{ old('archive_author', $archive->authors) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="archive_title">Thesis Title</label>
                                        <textarea class="form-control input-square" id="archive_title" name="archive_title" rows="3" required>{{ old('archive_title', $archive->title) }}</textarea>
                                    </div>
                                </div>







                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="archive_subject">Subject</label>
                                        <textarea class="form-control input-square" id="archive_subject" name="archive_subject" rows="5" required>{{ old('archive_subject', $archive->subject) }}</textarea>
                                    </div>
                                </div>


                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label for="archive_citation">Recommend Citation</label>
                                        <textarea class="form-control input-square" id="archive_citation" name="archive_citation" rows="5" required>{{ old('archive_citation', $archive->subject) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Files Upload</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">



                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="thesis_file">Upload Thesis File</label>

                                        @if (!empty($archive->thesis_file))
                                            <div id="currentFile" class="mb-2">
                                                <a href="{{ asset('storage/' . $archive->thesis_file) }}" target="_blank"
                                                    class="text-info">
                                                    <i class="fa fa-eye"></i> view_thesis_file.pdf
                                                </a>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control-file" id="thesis_file"
                                            name="thesis_file" accept="application/pdf"
                                            {{ empty($archive->thesis_file) ? 'required' : '' }} />
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="tables_file">Upload Tables File</label>
                                        <br>

                                        {{-- Show link to current file if there is one --}}
                                        @if (!empty($archive->tables_file) && \Illuminate\Support\Facades\Storage::disk('public')->exists($archive->tables_file))
                                            <div id="currentFile" class="mb-2">
                                                <a href="{{ asset('storage/' . $archive->tables_file) }}" target="_blank"
                                                    class="text-info">
                                                    <i class="fa fa-eye"></i> view_table_file.pdf
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" class="form-control-file" id="tables_file"
                                            name="tables_file" accept="application/pdf"
                                            {{ empty($archive->tables_file) ? '' : '' }} />
                                    </div>
                                </div>




                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="figures_file">Upload Figures File</label>
                                        <br>

                                        @if (
                                            !empty($archive->figures_file) &&
                                                \Illuminate\Support\Facades\Storage::disk('public')->exists($archive->figures_file))
                                            <div id="currentFile" class="mb-2">
                                                <a href="{{ asset('storage/' . $archive->figures_file) }}"
                                                    target="_blank" class="text-info">
                                                    <i class="fa fa-eye"></i> view_figures_file.pdf
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" class="form-control-file" id="figures_file"
                                            name="figures_file" accept="application/pdf"
                                            {{ empty($archive->figures_file) ? '' : '' }} />
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="recommendation_file">Upload Recommendation File</label>
                                        <br>

                                        @if (
                                            !empty($archive->recommendation_file) &&
                                                \Illuminate\Support\Facades\Storage::disk('public')->exists($archive->recommendation_file))
                                            <div id="currentFile" class="mb-2">
                                                <a href="{{ asset('storage/' . $archive->recommendation_file) }}"
                                                    target="_blank" class="text-info">
                                                    <i class="fa fa-eye"></i> view_recommendation_file.pdf
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" class="form-control-file" id="recommendation_file"
                                            name="recommendation_file" accept="application/pdf"
                                            {{ empty($archive->recommendation_file) ? '' : '' }} />
                                    </div>
                                </div>

                                <div class="separator-solid"></div>

                                <div class="col-md-12 mt-2">
                                    <input class="btn btn-primary" type="submit" value="Submit">
                                    <button type="button" class="btn btn-danger"
                                        onclick="window.location='{{ route('staff.archive.manage') }}'">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </form>
    </div>
@endsection

@push('script')
    <script>
        $('#archive_year').datetimepicker({
            format: 'YYYY',
            viewMode: 'years'
        }).on('dp.show', function(e) {
            // force focus back to years panel
            var picker = $(this).data("DateTimePicker");
            picker.viewMode("years");
        });
        $('#datepicker').datetimepicker({
            format: 'MM/DD/YYYY',
        });


        $('#multiple').select2({
            theme: "bootstrap",
            placeholder: $('#multiple').data('placeholder'),
            allowClear: true
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

@endpush
