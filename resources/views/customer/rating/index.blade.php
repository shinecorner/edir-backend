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
								<li><a href="{{ route('customer.rating') }}">Ratings</a></li>
								<li class="active">Übersicht</li>
							</ol>
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
                                <th><i class="fa fa-eye"></i></th>
                                <th>Erstellt am</th>
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
            url: "{!! route('customer.rating.datatables') !!}"
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'companyname', name: 'companyname'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'rating', name: 'rating'},
            {data: 'is_visible', name: 'is_visible'},
            {data: 'created_at', name: 'created_at'},
        ],
        order: [[0, "asc"]],
    });
});
</script>
@endsection