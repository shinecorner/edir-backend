@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Unterkategorien</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('category.secondary') }}">Unterkategorien</a></li>
								<li class="active">Übersicht</li>
							</ol>
						</div>
						<div class="tbl-cell tbl-cell-action">
							<a href="{!! route('category.secondary.form') !!}" class="btn btn-primary-outine btn-rounded pull-right m-l-1">
                                <i class="fa fa-plus-circle m-r-1"></i>  Neue Unterkategorie
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
                                <th>Hauptkat.</th>
                                <th>SEO Url</th>
                                <th>Firmen</th>
                                <th>Beschreibung</th>
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
            url: "{!! route('category.secondary.datatables') !!}"
        },
        columns: [
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'categoryprimary', name: 'category_primaries.name'},
            {data: 'slug', name: 'slug'},
            {data: 'count', name: 'count', searchable: false},
            {data: 'description', name: 'description', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[2, "asc"]],
        createdRow: function( row, data, dataIndex ) {
            $(row).find('td:first-child').addClass('table-photo');
        }
    });
});
</script>
@endsection