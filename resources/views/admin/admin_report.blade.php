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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Top Views & Search</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <a href="{{ route('admin.reports.archives.top_views_export') }}"
                                            class="btn btn-primary btn-round">
                                            DOWNLOAD XLS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Monthly User Report</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="dateMonthly" name="dateMonthly">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <a href="" id="btnDownloadPatrons" class="btn btn-primary btn-round">
                                            DOWNLOAD XLS
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yearly Archive Report</h4>
                    </div>
                     <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="dateYearly" name="dateYearly">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <a href="" id="btnDownloadYearly" class="btn btn-primary btn-round">
                                            DOWNLOAD XLS
                                        </a>
                                    </div>

                                </div>
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
        $(document).ready(function() {
            var now = moment();
        
            $('#dateMonthly').val(now.format('MM'));

          
            $('#dateMonthly').datetimepicker({
                format: 'MM',
                viewMode: 'months',
                defaultDate: now,
                useCurrent: false
            }).on('dp.show', function(e) {
                
                $(this).data('DateTimePicker').viewMode('months');
            }).on('dp.change', function(e) {
            
                updateDownloadLink();
            });

            
            $('#dateMonthly').on('change', updateDownloadLink);

          
            updateDownloadLink();

            function updateDownloadLink() {
                var m = $('#dateMonthly').val();
                var month = parseInt(m, 10);
                if (!isNaN(month) && month >= 1 && month <= 12) {
                    var base = '{{ url('admin/reports/user') }}';
                    var url = base + '/' + month;
                    $('#btnDownloadPatrons').attr('href', url);
                } else {
                    $('#btnDownloadPatrons').attr('href', '#');
                }
            }
        });


     $(document).ready(function() {
    var now = moment();

    // Set input value to current year
    $('#dateYearly').val(now.format('YYYY'));

    // Initialize year picker
    $('#dateYearly').datetimepicker({
        format: 'YYYY',
        viewMode: 'years',
        defaultDate: now,
        useCurrent: false
    }).on('dp.show', function(e) {
        $(this).data('DateTimePicker').viewMode('years');
    }).on('dp.change', function(e) {
        updateDownloadLink();
    });

    // Fallback for manual input
    $('#dateYearly').on('change', updateDownloadLink);

    // Initial link setup
    updateDownloadLink();

    function updateDownloadLink() {
        var y = $('#dateYearly').val();           // get year
        var year = parseInt(y, 10);              
        var currentYear = moment().year();
        if (!isNaN(year) && year >= 1900 && year <= currentYear) {
            var base = '{{ url("admin/reports/archives") }}';
            var url = base + '/' + year;          // use year instead of month
            $('#btnDownloadYearly').attr('href', url);
        } else {
            $('#btnDownloadYearly').attr('href', '#');
        }
    }
});

    </script>
@endpush
