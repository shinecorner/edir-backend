<div class="row address-row">
    <!-- Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="mapModalLabel">Map Voransicht</h4>
          </div>
          <div class="modal-body">
            {{ Form::Finput('Ihre Anschrift', 'autocomplete', null, ['id' => 'autocomplete']) }}
            <div id="map" style="min-height: 350px;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Adresse übernehmen</button>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">{{ Form::Finput('Anschrift', 'address', isset($data) ? $data->address_line : null, ['id' => 'street-name']) }}</div>
    <div class="col-md-6">{{ Form::Finput('Adress-Zusatz', 'location[street_additional]') }}</div>

    {{ Form::hidden('location[street_name]') }}
    {{ Form::hidden('location[street_number]') }}
    {{ Form::hidden('location[zip_code]') }}
    {{ Form::hidden('location[city]') }}
    {{ Form::hidden('location[district]') }}
    {{ Form::hidden('location[county]') }}
    {{ Form::hidden('location[state]') }}
    {{ Form::hidden('location[latitude]') }}
    {{ Form::hidden('location[longitude]') }}
</div>