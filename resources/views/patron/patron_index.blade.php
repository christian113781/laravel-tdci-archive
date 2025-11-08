@extends('patron.patron_dashboard')

@section('pages')
    <div class="page-inner">
        <div class="row ">

            <!-- Modal HTML (with id “announcementListModal”) -->
            <div class="modal fade" id="announcementListModal" tabindex="-1" aria-labelledby="announcementListModalLabel"
                aria-hidden="true">
                <div class="modal-dialog  modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="announcementListModalLabel">
                                <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                Announcements
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body ">
                            <div style="max-height: 400px; ">
                                @forelse ($announcements as $announcement)
                                    <div class="mx-auto announcement-card" data-id="{{ $announcement->id }}"
                                        style="cursor: pointer;">
                                        <div class="card-body alert alert-warning">
                                            <h5 class="card-title fs-6 text-justify">{{ $announcement->title }}</h5>
                                            <small class="text-muted">Posted on
                                                {{ $announcement->created_at->format('M j, Y') }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <p>No announcements available.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
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
                                    <p class="card-category">Published Archive</p>
                                    <h4 class="card-title">{{ $archiveCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body ">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">My Bookmarks</p>
                                    <h4 class="card-title">{{ $bookmarkCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Archive Request</p>
                                    <h4 class="card-title">{{ $requestCount }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Latest Uploads</div>
                    </div>
                    <div class="card-body">
                        <div class="accordion accordion-black">
                            @foreach ($archives as $archive)
                                <div class="card mb-4">
                                    <div class="card-header" id="heading{{ $archive->id }}" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $archive->id }}" aria-expanded="true"
                                        aria-controls="collapse{{ $archive->id }}">
                                        <div class="span-title d-flex align-items-start">
                                            <i class="fas fa-archive me-2 mt-1"></i>
                                            <div>{{ $archive->title }}</div>
                                        </div>
                                        <div class="span-mode"></div>
                                    </div>

                                    <div id="collapse{{ $archive->id }}" class="collapse"
                                        aria-labelledby="heading{{ $archive->id }}" data-parent="#accordion">
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
                                                <a href="{{ route('patron.archive.details', $archive->id) }}"
                                                    class="btn btn-sm btn-primary">View Archive Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach



                        </div>

                        {{ $archives->links('pagination::bootstrap-4') }}




                        {{-- {{ $archives->link() }} --}}



                        {{-- <nav aria-label="...">
                            <ul class="pagination pg-warning mb-0">
                                <li class="page-item">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1 <span
                                            class="sr-only">(current)</span></a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav> --}}

                    </div>
                </div>
            </div>



        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#owl-demo').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            autoplay: true,
            autoplayTimeout: 1000,
            autoplayHoverPause: true,

            // Try enabling autoWidth
            autoWidth: true,

            responsive: {
                0: {
                    items: 1 // one image per slide on small screens
                },
                600: {
                    items: 2 // fewer images at once so each is bigger
                },
                1000: {
                    items: 3 // you could also try 2 or 3 instead of 4
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.announcement-card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if (id) {
                        // Build the URL using Laravel route pattern
                        const url = "{{ url('/patron/announcement') }}/" + id;
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Only show if there are announcements
            @if (isset($announcements) && $announcements->count() > 0)
                var modalEl = document.getElementById('announcementListModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            @endif
        });
    </script>
@endpush
