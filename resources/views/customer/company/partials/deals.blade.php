    <div class="row text-right tab-dt-button">
        <a href="{!! route('customer.deal.form') !!}" class="btn btn-sm btn-primary-outine btn-rounded pull-right m-l-1">
            <i class="fa fa-plus-circle m-r-1"></i>  Neuer Deal
        </a>
    </div>
    <table class="display table table-striped tab-dt" cellspacing="0" width="100%" id="dealsDataTable">
        <thead class="thead-default">
            <tr>
                <th>#</th>
                <th>Kategorie</th>
                <th>Titel</th>
                <th>Gültigkeit</th>
                <th>Ok</th>
                <th>Erstellt am</th>
                <th>Optionen</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@section('partials-js')
@parent
<script>
$(function() {
    $('#dealsDataTable').DataTable({
        dom: "<'row'<'col-sm-6 text-left'f><'col-sm-6'>><'row'<'col-sm-12'tr>>",
        lengthChange: false,
        paging:false,
        info: false,
        processing: true,
        serverSide: true,
        ajax: {
            type: "POST",
            url: "{!! route('customer.deal.datatables') !!}",
            data: function ( d ) {
                d.company_id = '{{ isset($data) ? $data->id : null }}'
            }
        },
        columns: [
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'category', name: 'category.name'},
            {data: 'title', name: 'title'},
            {data: 'date', name: 'date'},
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