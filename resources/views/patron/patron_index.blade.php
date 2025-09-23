@extends('patron.patron_dashboard')

@section('index')
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
            </div>

        </div>


        <div class="row ">
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><b>Total Archive</b></h5>
                                <p class="text-muted">All Published Value</p>
                            </div>
                            <h3 class="text-info fw-bold">{{ $publishedArchiveCount }}</h3>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <p class="text-muted mb-0">Change</p>
                            <p class="text-muted mb-0">75%</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><b>Total Published</b></h5>
                                <p class="text-muted">All Published Value</p>
                            </div>
                            <h3 class="text-success fw-bold">{{ $publishedArchiveCount }}</h3>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success w-25" role="progressbar" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <p class="text-muted mb-0">Change</p>
                            <p class="text-muted mb-0">25%</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><b>My Archive Request</b></h5>
                                <p class="text-muted">All Request Value</p>
                            </div>
                            <h3 class="text-danger fw-bold">{{ $requestCount }}</h3>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <p class="text-muted mb-0">Change</p>
                            <p class="text-muted mb-0">50%</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5><b>Total Pending Request</b></h5>
                                <p class="text-muted">All Pending Request</p>
                            </div>
                            <h3 class="text-secondary fw-bold">{{ $requestPendingCount }}</h3>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-secondary w-25" role="progressbar" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <p class="text-muted mb-0">Change</p>
                            <p class="text-muted mb-0">25%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
