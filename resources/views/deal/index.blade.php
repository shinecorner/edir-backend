@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Deals</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('deal') }}">Deals</a></li>
								<li class="active">Übersicht</li>
							</ol>
						</div>
						<div class="tbl-cell tbl-cell-action">
							<a href="{!! route('deal.form') !!}" class="btn btn-primary-outine btn-rounded pull-right m-l-1">
                                <i class="fa fa-plus-circle m-r-1"></i>  Neuer Deal
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
                                <th>Kategorie</th>
                                <th>Name</th>
                                <th>Firma</th>
                                <th>Gültigkeit</th>
                                <th>Discount</th>
                                <th>Sichtbar</th>
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
            url: "{!! route('deal.datatables') !!}"
        },
        columns: [
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'category', name: 'category.name'},
            {data: 'name', name: 'name'},
            {data: 'company', name: 'company.name'},
            {data: 'date', name: 'date'},
            {data: 'discount', name: 'discount'},
            {data: 'active', name: 'active'},
            {data: 'approved', name: 'approved'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, "asc"]],
        createdRow: function( row, data, dataIndex ) {
            $(row).find('td:first-child').addClass('table-photo');
        }
    });
});
</script>
@endsection