@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Ratings</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('rating') }}">Ratings</a></li>
								<li class="active">Übersicht</li>
							</ol>
						</div>
						<div class="tbl-cell tbl-cell-action">
							<a href="{!! route('rating.form') !!}" class="btn btn-primary-outine btn-rounded pull-right m-l-1">
                                <i class="fa fa-plus-circle m-r-1"></i>  Neues Rating
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
                                <th>Firma</th>
                                <th>Titel</th>
                                <th>Text</th>
                                <th>Rating</th>
                                <th>Ok</th>
                                <th>Erstellt am</th>
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
            url: "{!! route('rating.datatables') !!}"
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'company', name: 'company.name'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'rating', name: 'rating'},
            {data: 'approved', name: 'approved'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, "asc"]],
    });
});
</script>
@endsection