@php
    $role = auth()->check() ? auth()->user()->role : 'patron';
    if (!in_array($role, ['admin', 'staff'])) {
        $role = 'patron';
    }
    $layout = "{$role}.{$role}_dashboard";
@endphp

<style>
    .table-border-thin {
        border-collapse: collapse;
    }

    .table-border-thin th,
    .table-border-thin td {
        border: 1.5px solid #dee2e6;
    }

    .table {
        --bs-table-color: inherit !important;
    }

    table th {
        white-space: nowrap;
        border: 1px solid lightgray;
        min-width: 140px;
        width: 140px;
        color: #555555;


    }

    table td {
        text-align: justify;
        word-spacing: 5px;
    }

    .file-attach {
        display: inline-block;
    }

    .file-title {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 8px;
    }

    .file-body {
        border-right: 1px solid #ddd;
        border-bottom: 5px solid #1a2035;
        border-left: 1px solid #ddd;
        padding: 10x;
    }

    .file-icon {
        font-size: 40px;
        padding: 10px;
    }

    .status-box {
        display: inline-block;
        width: auto;
        padding: 3px 10px;
        border-radius: 4px;
    }


    .status-box.publish {
        background-color: #dff0d8;
        color: #3c763d;
    }

    .status-box.unpublish {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }
</style>

@php
    use Milon\Barcode\DNS1D;
    $status = $archive->status ?? 'UNKNOWN';
    $statusClass = strtolower($status);

    // Generate barcode base64
    $d = new DNS1D();
    $d->setStorPath(storage_path('app/public/barcodes/'));
    $barcodeBase64 = $d->getBarcodePNG($archive->archive_code, 'C128', 3, 50);
@endphp

@extends($layout)

@section('pages')
    <div class="page-inner ms-lg-0">
        <div class="page-header ">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Archive</li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>

        <div class="row">

            <div class="col-md-12 mb-3 d-flex justify-content-between">
                <a href="{{ url()->previous() }}" class="btn btn-black  text-white">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>

            @session('error')
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show  mx-auto" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endsession


            <div class="d-flex justify-content-between align-items-center mb-3 mt-2 ">

                <div class="status-box {{ $statusClass }}">
                    <span>Status: </span>
                    <span>{{ $status }}</span>
                </div>


                <div>
                    @if (auth()->user()->role !== 'patron')
                        <a href="data:image/png;base64,{{ $barcode }}"
                            download="{{ $archive->archive_code }}_barcode.png" class="btn btn-black">
                            Download Barcode
                        </a>
                    @endif
                </div>
            </div>


            <div class="col-xs-12 col-md-10">
                <table class="table alert alert-black" style="border-top: 1px solid #1a2035;">
                    <tr>
                        <th>Authors</th>
                        <td>{{ $archive->authors ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th>Title</th>
                        <td>{{ $archive->title ?? 'untitled' }}</td>
                    </tr>
                    <tr>
                        <th>Year Issued</th>
                        <td>{{ $archive->year ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td><span class="txt-info">{{ $archive->subject ?? 'no subject available.' }}</span></td>
                    </tr>
                    <tr>
                        <th>Program</th>
                        <td>{{ $archive->program->name ?? 'n/a' }}</td>
                    </tr>
                    <tr>
                        <th>Citation</th>
                        <td>{{ $archive->citation ?? 'No Citaion' }}</td>

                    </tr>

                    <tr>
                        <th>Cataloguing</th>
                        <td>
                            @if ($archive->user)
                                {{ $archive->user->first_name }}
                                {{ $archive->user->last_name }}
                            @else
                                n/a
                            @endif
                        </td>
                    </tr>


                </table>
            </div>

            <div class="col-12 col-md-2">
                <div class="row">
                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Thesis File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                @if (!empty($archive->thesis_file))
                                    <a
                                        href="{{ route('archive.download', ['archive' => $archive->id, 'section' => 'thesis']) }}">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </a>
                                @else
                                    <i class="fas fa-file-pdf text-muted"></i>
                                @endif
                            </div>
                            <div class="file-caption">
                                {{ $thesisSize }}
                            </div>
                        </div>
                    </div>

                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Tables File
                        </div>

                        <div class="file-body bg-white">
                            <div class="file-icon">
                                @if (!empty($archive->tables_file))
                                    <a
                                        href="{{ route('archive.download', ['archive' => $archive->id, 'section' => 'tables']) }}">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </a>
                                @else
                                    <i class="fas fa-file-pdf text-muted"></i>
                                @endif
                            </div>
                            <div class="file-caption">
                                {{ $tablesSize }}
                            </div>
                        </div>
                    </div>

                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Figures File
                        </div>

                        <div class="file-body bg-white">
                            <div class="file-icon">
                                @if (!empty($archive->figures_file))
                                    <a
                                        href="{{ route('archive.download', ['archive' => $archive->id, 'section' => 'figures']) }}">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </a>
                                @else
                                    <i class="fas fa-file-pdf text-muted"></i>
                                @endif
                            </div>
                            <div class="file-caption">
                                {{ $figuresSize }}
                            </div>
                        </div>


                    </div>

                </div>
            </div>

            <div class="col-xs-12 col-md-10">
                <div class="bg-white p-3 mb-3 alert alert-black">
                    <div class="row">
                        <div class="col-xs-6">

                            <div>
                                <span><i>Keywords </i>:</span>
                                <span class="txt-info">
                                    <td>
                                        @if ($archive->keywords->isEmpty())
                                            n/a
                                        @else
                                            {{ $archive->keywords->pluck('name')->join(', ') }}
                                        @endif
                                    </td>

                                </span>
                            </div>
                            <div>&nbsp;</div>
                            <div>
                                <span><strong>Access Permission : </strong></span>
                                <span class="">
                                    <span class="txt-info">

                                        @if ($archive->category === 'a')
                                            General
                                        @else
                                            Limited Access
                                        @endif

                                    </span>
                                </span>
                            </div>

                        </div>
                        <div class="col-xs-6">
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-md-2">
                <div class="row mb-5">

                    <div class="file-attach p-10 col-md-12 text-center">
                        <div class="file-title bg-white">
                            Recommendation File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                @if (!empty($archive->recommendation_file))
                                    <a href="{{ asset('storage/' . $archive->recommendation_file) }}" target="_blank"
                                        rel="noopener">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </a>
                                @else
                                    <i class="fas fa-file-pdf text-muted"></i>
                                @endif
                            </div>
                            <div class="file-caption">
                                {{ $recommendationSize }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
@endsection

@push('script')
    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert-danger').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 2000);
    </script>
@endpush
