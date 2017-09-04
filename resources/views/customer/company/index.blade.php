@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Firmen</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('customer.company') }}">Firmen</a></li>
								<li class="active">Übersicht</li>
							</ol>
						</div>
						<div class="tbl-cell tbl-cell-action">
							<a href="{!! route('customer.company.form') !!}" class="btn btn-primary-outine btn-rounded pull-right m-l-1">
                                <i class="fa fa-plus-circle m-r-1"></i> Neue Firma
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
                                <th>Anschrift</th>
                                <th>Plz</th>
                                <th>Ort</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th>Telefax</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th></th>
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
            url: "{!! route('customer.company.datatables') !!}"
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'address', name: 'address'},
            {data: 'zip_code', name: 'locations.zip_code'},
            {data: 'city', name: 'locations.city'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'fax', name: 'fax'},
            {data: 'listing_level', name: 'listing_level'},
            {data: 'listing_status', name: 'listing_status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection