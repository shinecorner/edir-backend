@extends('layouts.master')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            <header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Audits</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="{{ route('audit') }}">Audits</a></li>
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
                                <th> </th>
                                <th>#</th>
                                <th>Typ</th>
                                <th>Item</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Alt</th>
                                <th>Neu</th>
                                <th>Url</th>
                                <th>IP</th>
                                <th>Datum</th>
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
    function format ( d ) {
        var html = '<table  class="table-bordered" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; width: 100%">';

            $.each(d.old_values, function( index, value ) {
                html += '<tr>' +
                            '<td><strong>Feldname</strong></td>' +
                            '<td>' + index + '</td>' +
                            '<td><strong>Alter Wert:</strong></td>' +
                            '<td>' + value.old + '</td>' +
                            '<td><strong>Neuer Wert:</strong></td>' +
                            '<td>' + value.new + '</td>' +
                        '</tr>';
            });

            html +='</tr></table>';

            return html;
    }

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: "POST",
            url: "{!! route('audit.datatables') !!}"
        },
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": ''
            },
            {data: 'id', name: 'id'},
            {data: 'type', name: 'auditable_type'},
            {data: 'item', name: 'item', orderable: false, searchable: false},
            {data: 'user', name: 'user.email'},
            {data: 'event', name: 'event'},
            {data: 'old_values', name: 'old_values', visible: false},
            {data: 'new_values', name: 'new_values', visible: false},
            {data: 'url', name: 'url', orderable: false, searchable: false},
            {data: 'ip_address', name: 'ip_address'},
            {data: 'created_at', name: 'created_at'},
        ],
        order: [[10, 'desc']]
    });

    $('#dataTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }

    });
});
</script>
@endsection