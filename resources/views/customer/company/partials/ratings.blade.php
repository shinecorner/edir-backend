
    <table class="display table table-striped tab-dt" cellspacing="0" width="100%" id="ratingDataTable">
        <thead class="thead-default">
            <tr>
                <th>#</th>
                <th>Titel</th>
                <th>Bewertung</th>
                <th>Rating</th>
                <th>Ok</th>
                <th>Erstellt am</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@section('partials-js')
@parent
<script>
$(function() {
    $('#ratingDataTable').DataTable({
        dom: "<'row'<'col-sm-6 text-left'f><'col-sm-6'>><'row'<'col-sm-12'tr>>",
        lengthChange: false,
        paging:false,
        info: false,
        processing: true,
        serverSide: true,
        ajax: {
            type: "POST",
            url: "{!! route('customer.rating.datatables') !!}",
            data: function ( d ) {
                d.company_id = '{{ isset($data) ? $data->id : null }}'
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'rating', name: 'rating'},
            {data: 'approved', name: 'approved'},
            {data: 'created_at', name: 'created_at'},
        ]
    });
});
</script>
@endsection