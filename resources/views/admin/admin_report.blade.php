@extends('admin.admin_dashboard')

@section('pages')
<div class="page-inner">
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Manage</li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>
    </div>

    <!-- Archive Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-chart-bar me-2 text-primary"></i>Archive Reports</h5>
            <hr class="mb-3">
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-eye me-2 text-info"></i>Top Viewed Archives</h5>
                    <small class="text-muted d-block">Export the 10 most viewed archives by date range</small>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Program</label>
                        <select class="form-select form-control" id="programSelectTopViews">
                            <option value="">All Programs</option>
                            @php
                            $programs = \App\Models\Program::all();
                            @endphp
                            @foreach ($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateFromTopViews"
                                        name="dateFromTopViews" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateToTopViews" name="dateToTopViews"
                                        placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="" id="btnDownloadTopViews" class="btn btn-info btn-round w-100">
                        <i class="fa fa-download me-2"></i>DOWNLOAD XLS
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-file-archive me-2 text-warning"></i>Publication
                        Inventory</h5>
                    <small class="text-muted d-block">Filter archives by program and year</small>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Program</label>
                        <select class="form-select form-control" id="programSelectYearly">
                            <option value="">All Programs</option>
                            @php
                            $programs = \App\Models\Program::all();
                            @endphp
                            @foreach ($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Year</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="yearSelectYearly" name="yearSelectYearly"
                                placeholder="YYYY">
                            <span class="input-group-text">
                                <i class="fa fa-calendar-check"></i>
                            </span>
                        </div>
                    </div>
                    <a href="" id="btnDownloadYearly" class="btn btn-warning btn-round w-100">
                        <i class="fa fa-download me-2"></i>DOWNLOAD XLS
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- User Reports Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-users me-2 text-success"></i>User Reports</h5>
            <hr class="mb-3">
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2 text-success"></i>User Report
                    </h5>
                    <small class="text-muted d-block">Patron unique users by date range</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateFromMonthly" name="dateFromMonthly"
                                        placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateToMonthly" name="dateToMonthly"
                                        placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="" id="btnDownloadPatrons" class="btn btn-success btn-round w-100">
                        <i class="fa fa-download me-2"></i>DOWNLOAD XLS
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-search me-2 text-secondary"></i>Most Searched
                    </h5>
                    <small class="text-muted d-block">Top searches by date range</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>From Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateFromSearched"
                                        name="dateFromSearched" placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>To Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateToSearched" name="dateToSearched"
                                        placeholder="YYYY-MM-DD">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="" id="btnDownloadSearched" class="btn btn-secondary btn-round w-100">
                        <i class="fa fa-download me-2"></i>DOWNLOAD XLS
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-cog me-2 text-danger"></i>System Management</h5>
            <hr class="mb-3">
        </div>
    </div>

    <div class="row">

            {{-- <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-database me-2 text-primary"></i>SQL Restore</h5>
                    <small class="text-muted d-block">Restore database only from an SQL dump file</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.restore.sql') }}" method="POST" enctype="multipart/form-data"
                        id="restoreSqlForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label mb-2"><i class="fas fa-file-code me-1"></i>Select SQL
                                File</label>
                            <input type="file" name="sql_file" class="form-control" accept=".sql" required>
                            <small class="text-muted d-block mt-2">Only .sql SQL dump files are supported</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-round w-100" id="restoreSqlSubmitBtn"
                            onclick="return confirm('This will restore your database only from the SQL file. Continue?')">
                            <i class="fa fa-upload me-2"></i>RESTORE DATABASE FROM SQL
                        </button>
                    </form>
                </div>
            </div>
        </div>
       
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-redo me-2 text-warning"></i>System Restore</h5>
                    <small class="text-muted d-block">Restore archive files from a backup ZIP file (files only)</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.restore') }}" method="POST" enctype="multipart/form-data"
                        id="restoreForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label mb-2"><i class="fas fa-file-zip me-1"></i>Select Backup
                                File</label>
                            <input type="file" name="backup_file" class="form-control" accept=".zip" required>
                            <small class="text-muted d-block mt-2">Only .zip archive backup files are supported. This restore only restores files; use SQL Restore below for database data.</small>
                        </div>
                        <button type="submit" class="btn btn-danger btn-round w-100 text-white" id="restoreSubmitBtn"
                            onclick="return confirm('This will restore your system from the backup. Continue?')">
                            <i class="fa fa-upload me-2"></i>RESTORE FROM BACKUP
                        </button>
                    </form>
                </div>
            </div>
        </div> --}}

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-redo-alt me-2 text-danger"></i>Full System Restore</h5>
                    <small class="text-muted d-block">Restore both the database and application files from a backup ZIP file.</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.restore.full') }}" method="POST" enctype="multipart/form-data"
                        id="restoreFullForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label mb-2"><i class="fas fa-file-zip me-1"></i>Select Backup File</label>
                            <input type="file" name="backup_file" class="form-control" accept=".zip" required>
                            <small class="text-muted d-block mt-2">Only .zip backup files are supported. This process will restore both the database and files contained in the backup.</small>
                        </div>
                        <button type="submit" class="btn btn-danger btn-round w-100 text-white" id="restoreFullSubmitBtn"
                            onclick="return confirm('This will restore your entire system including database and files from the backup. Continue?')">
                            <i class="fa fa-upload me-2"></i>RESTORE FULL SYSTEM FROM BACKUP
                        </button>
                    </form>
                </div>
            </div>
        </div>

         <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="fas fa-shield-alt me-2 text-success"></i>System Backup</h5>
                    <small class="text-muted d-block">Backup all database and archive files</small></small>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Create a complete backup of your
                        system data</p>
                    <a href="{{ route('admin.backup') }}" target="_blank" class="btn btn-success btn-round w-100"
                        id="btnBackupSystem"
                        onclick="return confirm('This will create a full system backup. Continue?')">
                        <i class="fa fa-download me-2"></i>CREATE & DOWNLOAD BACKUP
                    </a>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection
@push('script')
<script>
    $(document).ready(function() {
            var now = moment();
            var startDate = now.clone().subtract(30, 'days');

            // Set default date range (last 30 days)
            $('#dateFromMonthly').val(startDate.format('YYYY-MM-DD'));
            $('#dateToMonthly').val(now.format('YYYY-MM-DD'));

            // Initialize From Date picker
            $('#dateFromMonthly').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: startDate,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateDownloadLink();
            });

            // Initialize To Date picker
            $('#dateToMonthly').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: now,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateDownloadLink();
            });

            // Update download link when dates change
            $('#dateFromMonthly, #dateToMonthly').on('change', updateDownloadLink);

            // Initial link setup
            updateDownloadLink();

            function updateDownloadLink() {
                var fromDate = $('#dateFromMonthly').val();
                var toDate = $('#dateToMonthly').val();

                if (fromDate && toDate) {
                    var base = '{{ route('admin.reports.patrons.by_date_range') }}';
                    var url = base + '?from=' + fromDate + '&to=' + toDate;
                    $('#btnDownloadPatrons').attr('href', url);
                } else {
                    $('#btnDownloadPatrons').attr('href', '#');
                }
            }
        });


        $(document).ready(function() {
            var now = moment();
            var currentYear = now.format('YYYY');

            // Set default year (current year)
            $('#yearSelectYearly').val(currentYear);

            // Initialize Year picker
            var yearPicker = $('#yearSelectYearly').datetimepicker({
                format: 'YYYY',
                viewMode: 'years',
                defaultDate: now,
                useCurrent: false,
                keepInvalid: true,
                focusOnShow: true,
                ignoreReadonly: true,
                allowInputToggle: false
            });

            // Prevent switching to month/day view - always stay in year view
            yearPicker.on('dp.show', function(e) {
                $(this).data('DateTimePicker').viewMode('years');
            }).on('dp.change', function(e) {
                $(this).data('DateTimePicker').viewMode('years');
                updateDownloadLinkYearly();
            });

            // Update download link when year changes
            $('#yearSelectYearly').on('change', function() {
                $(this).data('DateTimePicker').viewMode('years');
                updateDownloadLinkYearly();
            });

            // Update download link when program changes
            $('#programSelectYearly').on('change', function() {
                updateDownloadLinkYearly();
            });

            // Initial link setup
            updateDownloadLinkYearly();

            function updateDownloadLinkYearly() {
                var year = $('#yearSelectYearly').val();
                var programId = $('#programSelectYearly').val();

                if (year) {
                    var base = '{{ route('admin.reports.archives.by_date_range') }}';
                    var fromDate = year + '-01-01';
                    var toDate = year + '-12-31';
                    var url = base + '?from=' + fromDate + '&to=' + toDate;

                    // Add program filter if selected
                    if (programId) {
                        url += '&program=' + programId;
                    }

                    $('#btnDownloadYearly').attr('href', url);
                } else {
                    $('#btnDownloadYearly').attr('href', '#');
                }
            }
        });

        $(document).ready(function() {
            var now = moment();
            var startDate = now.clone().subtract(30, 'days');

            // Set default date range (last 30 days)
            $('#dateFromSearched').val(startDate.format('YYYY-MM-DD'));
            $('#dateToSearched').val(now.format('YYYY-MM-DD'));

            // Initialize From Date picker
            $('#dateFromSearched').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: startDate,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateSearchDownloadLink();
            });

            // Initialize To Date picker
            $('#dateToSearched').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: now,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateSearchDownloadLink();
            });

            // Update download link when dates change
            $('#dateFromSearched, #dateToSearched').on('change', updateSearchDownloadLink);

            // Initial link setup
            updateSearchDownloadLink();

            function updateSearchDownloadLink() {
                var fromDate = $('#dateFromSearched').val();
                var toDate = $('#dateToSearched').val();

                if (fromDate && toDate) {
                    var base = '{{ route('admin.archive.export-most-searched') }}';
                    var url = base + '?from=' + fromDate + '&to=' + toDate;
                    $('#btnDownloadSearched').attr('href', url);
                } else {
                    $('#btnDownloadSearched').attr('href', '#');
                }
            }
        });

        $(document).ready(function() {
            var now = moment();
            var startDate = now.clone().subtract(30, 'days');

            // Set default date range (last 30 days)
            $('#dateFromTopViews').val(startDate.format('YYYY-MM-DD'));
            $('#dateToTopViews').val(now.format('YYYY-MM-DD'));

            // Initialize From Date picker
            $('#dateFromTopViews').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: startDate,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateTopViewsDownloadLink();
            });

            // Initialize To Date picker
            $('#dateToTopViews').datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: now,
                useCurrent: false
            }).on('dp.change', function(e) {
                updateTopViewsDownloadLink();
            });

            // Update download link when dates or program change
            $('#dateFromTopViews, #dateToTopViews, #programSelectTopViews').on('change',
                updateTopViewsDownloadLink);

            // Initial link setup
            updateTopViewsDownloadLink();

            function updateTopViewsDownloadLink() {
                var fromDate = $('#dateFromTopViews').val();
                var toDate = $('#dateToTopViews').val();
                var programId = $('#programSelectTopViews').val();

                if (fromDate && toDate) {
                    var base = '{{ route('admin.reports.archives.top_views_export') }}';
                    var url = base + '?from=' + fromDate + '&to=' + toDate;

                    // Add program filter if selected
                    if (programId) {
                        url += '&program=' + programId;
                    }

                    $('#btnDownloadTopViews').attr('href', url);
                } else {
                    $('#btnDownloadTopViews').attr('href', '#');
                }
            }
        });

        // ===== RESTORE FORM HANDLING WITH AJAX =====
        $('#restoreForm').on('submit', function(e) {
            e.preventDefault();

            console.log('[RESTORE] Form submitted');
            const fileInput = $('input[name="backup_file"]')[0];

            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                console.log('[RESTORE] File selected:', {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    sizeInMB: (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                });

                // Show confirmation alert
                swal({
                    title: "⚠️ WARNING",
                    text: "This will REPLACE all data with the backup. Continue?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: "Restore",
                            className: 'btn btn-warning'
                        }
                    }
                }).then((confirmed) => {
                    if (confirmed) {
                        // Create FormData for AJAX submission
                        const formData = new FormData($('#restoreForm')[0]);

                        // Show loading alert
                        swal({
                            title: "Restoring...",
                            text: "Please wait while your system is being restored from backup.",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: $('#restoreForm').attr('action'),
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                console.log('[RESTORE] Success:', response);

                                // SweetAlert for success
                                swal("Success!", response.message ||
                                    "System restored successfully from backup!", {
                                        icon: "success",
                                        buttons: {
                                            confirm: {
                                                className: 'btn btn-success'
                                            }
                                        }
                                    }).then(() => {
                                    // Reload page after success
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('[RESTORE] Error:', error);

                                // Get error message from response
                                let errorMessage = 'Restore failed. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    errorMessage = xhr.responseText;
                                }

                                // SweetAlert for error
                                swal("Error!", errorMessage, {
                                    icon: "error",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            } else {
                console.warn('⚠️ [RESTORE] No file selected!');
                swal("Warning!", "Please select a backup file.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            className: 'btn btn-warning'
                        }
                    }
                });
            }
        });

        // ===== SQL RESTORE FORM HANDLING WITH AJAX =====
        $('#restoreSqlForm').on('submit', function(e) {
            e.preventDefault();

            console.log('[RESTORE SQL] Form submitted');
            const fileInput = $('input[name="sql_file"]')[0];

            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                console.log('[RESTORE SQL] File selected:', {
                    name: file.name,
                    size: file.size,
                    type: file.type
                });

                swal({
                    title: "⚠️ WARNING",
                    text: "This will restore your database from the SQL file. Continue?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: "Restore",
                            className: 'btn btn-primary'
                        }
                    }
                }).then((confirmed) => {
                    if (confirmed) {
                        const formData = new FormData($('#restoreSqlForm')[0]);

                        swal({
                            title: "Restoring database...",
                            text: "Please wait while the database is being restored from the SQL file.",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: $('#restoreSqlForm').attr('action'),
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                console.log('[RESTORE SQL] Success:', response);

                                swal("Success!", response.message ||
                                    "Database restored successfully from SQL file!", {
                                        icon: "success",
                                        buttons: {
                                            confirm: {
                                                className: 'btn btn-success'
                                            }
                                        }
                                    }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('[RESTORE SQL] Error:', error);

                                let errorMessage = 'SQL restore failed. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    errorMessage = xhr.responseText;
                                }

                                swal("Error!", errorMessage, {
                                    icon: "error",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            } else {
                swal("Warning!", "Please select an SQL file.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            className: 'btn btn-warning'
                        }
                    }
                });
            }
        });

        $('#restoreFullForm').on('submit', function(e) {
            e.preventDefault();

            const fileInput = $('input[name="backup_file"]', '#restoreFullForm')[0];

            if (fileInput && fileInput.files.length > 0) {
                swal({
                    title: "⚠️ WARNING",
                    text: "This will restore the database and files from the backup ZIP. Continue?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: "Restore",
                            className: 'btn btn-danger'
                        }
                    }
                }).then((confirmed) => {
                    if (confirmed) {
                        const formData = new FormData($('#restoreFullForm')[0]);

                        swal({
                            title: "Restoring full backup...",
                            text: "Please wait while your database and files are restored.",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                            url: $('#restoreFullForm').attr('action'),
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                swal("Success!", response.message || "Full backup restored successfully!", {
                                    icon: "success",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-success'
                                        }
                                    }
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                let errorMessage = 'Full restore failed. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    errorMessage = xhr.responseText;
                                }

                                swal("Error!", errorMessage, {
                                    icon: "error",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            } else {
                swal("Warning!", "Please select a backup ZIP file for full restore.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            className: 'btn btn-warning'
                        }
                    }
                });
            }
        });

        // Log when file is selected
        $('input[name="backup_file"]').on('change', function(e) {
            if (this.files.length > 0) {
                const file = this.files[0];
                console.log('📂 [RESTORE] File selected in input:', {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    lastModified: new Date(file.lastModified).toISOString()
                });
            }
        });
</script>
@endpush