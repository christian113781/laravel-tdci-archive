@extends('patron.patron_dashboard')
@section('pages')
				<div class="page-inner">
					
					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title">{{ $announcement->title }}</h4>

								</div>
								<div class="card-body">
									<p class="card-text">{!! nl2br(e(str_replace('\\n', "\n", $announcement->message))) !!}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			
@endsection

@push('script')
    
@endpush