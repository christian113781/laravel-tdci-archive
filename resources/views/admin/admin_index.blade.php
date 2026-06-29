@extends('admin.admin_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Archive Dashboard</h3>
            </div>

        </div>
        <div class="row">


            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Archive</p>
                                    <h4 class="card-title">{{ $archiveCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body ">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Total Users</p>
                                    <h4 class="card-title">{{ $userCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Verified Patrons</p>
                                    <h4 class="card-title">{{ $verifiedPatronCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                    <i class="far fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Not Verified Patrons</p>
                                    <h4 class="card-title">{{ $notVerifiedPatronCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">User Statistics</div>
                            <div class="card-tools">
                                <a href="{{ route('admin.reports.patron_login_count_export') }}"
                                    class="btn btn-label-info btn-round btn-sm">
                                    <span class="btn-label">
                                        <i class="fa fa-print"></i>
                                    </span>
                                    Export
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Archive Views by Program</div>
                            <div class="card-tools">
                                <a href="{{ route('admin.reports.archives.program_views_export') }}"
                                    class="btn btn-label-info btn-round btn-sm">
                                    <span class="btn-label">
                                        <i class="fa fa-print"></i>
                                    </span>
                                    Export
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="barChart"></canvas>
                        </div>
                        <div id="barChartLegend"></div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Latest Uploads</div>
                    </div>
                    <div class="card-body">
                        <div class="accordion accordion-black" id="latestUploadsAccordion">
                            @forelse ($archives as $archive)
                                <div class="card mb-4">
                                    <div class="card-header" id="latestHeading{{ $archive->id }}" data-bs-toggle="collapse"
                                        data-bs-target="#latestCollapse{{ $archive->id }}" aria-expanded="true"
                                        aria-controls="latestCollapse{{ $archive->id }}">
                                        <div class="span-title d-flex align-items-start">
                                            <i class="fas fa-archive me-2 mt-1"></i>
                                            <div>{{ $archive->title }}</div>
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>

                                    <div id="latestCollapse{{ $archive->id }}" class="collapse"
                                        aria-labelledby="latestHeading{{ $archive->id }}"
                                        data-parent="#latestUploadsAccordion">
                                        <div class="card-body alert alert-warning">
                                            <div>
                                                <i class="fas fa-user-alt"></i>&nbsp;&nbsp;
                                                @if (str_contains($archive->authors, ','))
                                                    {{ $archive->authors }}
                                                @else
                                                    {{ $archive->authors }}
                                                @endif
                                                |
                                                &nbsp;<i class="fas fa-calendar"></i>&nbsp;{{ $archive->year }}
                                            </div>
                                            <div class="mt-3">
                                                {{ Str::limit($archive->subject, 200) }}
                                            </div>

                                            <div class="mt-3">
                                                <i class="fas fa-tags"></i>&nbsp;&nbsp; <span class="small">
                                                    {{ $archive->keywords->pluck('name')->implode(', ') }}</span>
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('admin.archive.details', $archive->id) }}"
                                                    class="btn btn-sm btn-primary">View Archive Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty

                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-archive fa-2x mb-2"></i>
                                    <p>No recent uploads available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Most Viewed Thesis</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="accordion accordion-black" id="programAccordion">
                            @forelse ($mostViewedArchives as $archive)
                                <div class="card mb-4 archive-item">
                                    <div class="card-header" id="programHeading{{ $archive->id }}"
                                        data-bs-toggle="collapse" data-bs-target="#programCollapse{{ $archive->id }}"
                                        aria-expanded="true" aria-controls="programCollapse{{ $archive->id }}">
                                        <div class="span-title d-flex align-items-start">
                                            <i class="fas fa-archive me-2 mt-1"></i>
                                            <div>{{ $archive->title }}</div>
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>

                                    <div id="programCollapse{{ $archive->id }}" class="collapse"
                                        aria-labelledby="programHeading{{ $archive->id }}"
                                        data-parent="#programAccordion">
                                        <div class="card-body alert alert-warning">
                                            <div>
                                                <i class="fas fa-user-alt"></i>&nbsp;&nbsp;
                                                @if (str_contains($archive->authors, ','))
                                                    {{ $archive->authors }}
                                                @else
                                                    {{ $archive->authors }}
                                                @endif
                                                |
                                                &nbsp;<i class="fas fa-calendar"></i>&nbsp;{{ $archive->year }}
                                            </div>
                                            <div class="mt-3">
                                                {{ Str::limit($archive->subject, 200) }}
                                            </div>

                                            <div class="mt-3">
                                                <i class="fas fa-tags"></i>&nbsp;&nbsp; <span class="small">
                                                    {{ $archive->keywords->pluck('name')->implode(', ') }}</span>
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('admin.archive.details', $archive->id) }}"
                                                    class="btn btn-sm btn-primary">View Archive Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty

                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-archive fa-2x mb-2"></i>
                                    <p>No recent uploads available.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>


        </div>

        @push('script')
            <script>
                var lineChart = document.getElementById('lineChart').getContext('2d'),
                    barChart = document.getElementById('barChart').getContext('2d');

                var myLineChart = new Chart(lineChart, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($patronLoginStats['months']) !!},
                        datasets: [{
                            label: "User Logins",
                            borderColor: "#1d7af3",
                            pointBorderColor: "#FFF",
                            pointBackgroundColor: "#1d7af3",
                            pointBorderWidth: 2,
                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 1,
                            pointRadius: 4,
                            backgroundColor: 'transparent',
                            fill: true,
                            borderWidth: 2,
                            data: {!! json_encode($patronLoginStats['patronLoginCount']) !!}
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 10,
                                fontColor: '#1d7af3',
                            }
                        },
                        tooltips: {
                            bodySpacing: 4,
                            mode: "nearest",
                            intersect: 0,
                            position: "nearest",
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10
                        },
                        layout: {
                            padding: {
                                left: 15,
                                right: 15,
                                top: 15,
                                bottom: 15
                            }
                        }
                    }
                });


                var myBarChart = new Chart(barChart, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(
                            is_array($viewsPerProgram['names'] ?? [])
                                ? array_map(function ($name) {
                                    $skipWords = ['of', 'and', 'the', 'in', 'or'];
                                    $words = explode(' ', $name);
                                    $abbr = '';
                                    foreach ($words as $word) {
                                        $lowerWord = strtolower($word);
                                        if (strlen($word) > 0 && !in_array($lowerWord, $skipWords)) {
                                            $abbr .= strtoupper($word[0]);
                                        }
                                    }
                                    return strlen($abbr) > 0 ? $abbr : substr($name, 0, 4);
                                }, $viewsPerProgram['names'])
                                : [],
                        ) !!},
                        datasets: [{
                            label: "Views",
                            labelColor: '#1d7af3',
                            backgroundColor: 'rgb(23, 125, 255)',
                            borderColor: 'rgb(23, 125, 255)',
                            borderWidth: 1,
                            data: {!! json_encode(is_array($viewsPerProgram['views'] ?? []) ? $viewsPerProgram['views'] : []) !!},
                            borderRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: "rgba(0,0,0,0.5)",
                                    fontStyle: "500"
                                },
                                gridLines: {
                                    display: true,
                                    drawBorder: false
                                }
                            }],
                            xAxes: [{
                                gridLines: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    fontColor: "rgba(0,0,0,0.5)",
                                    fontStyle: "500"
                                }
                            }]
                        },
                        plugins: {
                            legend: {
                                display: false,
                                position: 'bottom',
                                labels: {
                                    boxWidth: 15,
                                    padding: 15,
                                    fontColor: '#333',
                                    font: {
                                        size: 13
                                    }
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Views: ' + context.parsed.y;
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 15,
                                right: 15,
                                top: 15,
                                bottom: 15
                            }
                        }
                    }
                });


                // Program filter functionality
                document.getElementById('programFilter').addEventListener('change', function() {
                    const programId = this.value;
                    const accordion = document.getElementById('programAccordion');

                    if (programId === '') {
                        // Reset to all archives - reload page or restore original data
                        location.reload();
                    } else {
                        // Fetch archives for selected program
                        fetch('/admin/archives/by-program/' + programId)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Clear accordion
                                    accordion.innerHTML = '';

                                    if (data.archives.length === 0) {
                                        accordion.innerHTML = `
                                            <div class="text-center py-4 text-muted">
                                                <i class="fas fa-archive fa-2x mb-2"></i>
                                                <p>No archives found for this program.</p>
                                            </div>
                                        `;
                                    } else {
                                        // Add archives to accordion
                                        data.archives.forEach((archive, index) => {
                                            const archiveHTML = `
                                                <div class="card mb-4 archive-item">
                                                    <div class="card-header" id="programHeading${archive.id}" data-bs-toggle="collapse"
                                        data-bs-target="#programCollapse${archive.id}" aria-expanded="true"
                                        aria-controls="programCollapse${archive.id}">
                                                        <div class="span-title d-flex align-items-start">
                                                            <i class="fas fa-archive me-2 mt-1"></i>
                                                            <div>${archive.title}</div>
                                                        </div>
                                                        <div class="span-mode"></div>
                                                    </div>

                                                    <div id="programCollapse${archive.id}" class="collapse"
                                        aria-labelledby="programHeading${archive.id}" data-parent="#programAccordion">
                                                        <div class="card-body alert alert-warning">
                                                            <div>
                                                                <i class="fas fa-user-alt"></i>&nbsp;&nbsp;${archive.authors}
                                                                |
                                                                &nbsp;<i class="fas fa-calendar"></i>&nbsp;${archive.year}
                                                            </div>
                                                            <div class="mt-3">
                                                                ${archive.subject}
                                                            </div>

                                                            <div class="mt-3">
                                                                <i class="fas fa-tags"></i>&nbsp;&nbsp; <span class="small">
                                                                    ${archive.keywords}</span>
                                                            </div>

                                                            <div class="mt-3">
                                                                <a href="/admin/archive/details/${archive.id}"
                                                                    class="btn btn-sm btn-primary">View Archive Details</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                            accordion.innerHTML += archiveHTML;
                                        });

                                        // Re-initialize Bootstrap collapse for new elements
                                        const collapseElements = accordion.querySelectorAll(
                                            '[data-bs-toggle="collapse"]');
                                        collapseElements.forEach(el => {
                                            new bootstrap.Collapse(el.nextElementSibling, {
                                                toggle: false
                                            });
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                accordion.innerHTML = '<p class="text-danger">Error loading archives.</p>';
                            });
                    }
                });
            </script>
        @endpush
    @endsection
