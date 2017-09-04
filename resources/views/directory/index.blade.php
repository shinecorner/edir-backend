@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Directories</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('directory') }}">Directories</a></li>
								<li class="active">Übersicht</li>
							</ol>
						</div>
						<div class="tbl-cell tbl-cell-action">
							<a href="{!! route('directory.form') !!}" class="btn btn-primary-outine btn-rounded pull-right m-l-1">
                                <i class="fa fa-plus-circle m-r-1"></i> Neues Directory
                            </a>
						</div>
					</div>
				</div>
			</header>

			@include('layouts.partials.messages')

            <section class="card">
                <!--<div class="table-responsive">-->
                <div class="card-block">
                    <table class="display table table-striped" cellspacing="0" width="100%" id="dataTable">
                        <thead class="thead-default">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Api Token</th>
                                <th>Optionen</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                <!-- </div> -->
                </div>
            </section>

        </div>
    </div>
@stop

@section('custom-js-code')
<script>
$(function() {
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: "POST",
            url: "{!! route('directory.datatables') !!}"
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'api_token', name: 'api_token'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection