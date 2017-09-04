<?php

use GuzzleHttp\Client as HttpClient;
use Sleimanx2\Plastic\Facades\Map;
use Sleimanx2\Plastic\Facades\Plastic;
use Sleimanx2\Plastic\Map\Blueprint;
use Sleimanx2\Plastic\Mappings\Mapping;

class EdirectoryModelsCompany extends Mapping
{
    /**
     * Full name of the model that should be mapped
     *
     * @var string
     */
    protected $model = App\Models\Company::class;

    /**
     * @var
     */
    private $plasticClient;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * EdirectoryModelsCompany constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->plasticClient = Plastic::getClient();
        $this->httpClient = new HttpClient();
    }

    /**
     * Run the mapping.
     *
     * @return void
     */
    public function map()
    {
        //index delete we create with settings below
        if($this->plasticClient->indices()->exists(['index' => 'plastic'])) {
            $this->plasticClient->indices()->delete(['index'=> 'plastic']);
        }

        $httpResult = $this->httpClient->request('PUT', config('plastic.connection.hosts')[0] . '/' . config('plastic.index'), [
            'json' => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'case_insensitive_sort' => [
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        if($httpResult->getStatusCode() != 200) {
            echo $httpResult->getBody();
        }

        Map::create($this->getModelType(), function (Blueprint $map) {
            $map->long('id');

            $map->string('name', [
                'type' => 'string',
                'fielddata' => true,
                'fields' => [
                    'lower_case_sort' => [
                        'type' => 'string',
                        'fielddata' => true,
                        'analyzer' => 'case_insensitive_sort'
                    ]
                ]
            ]);

            $map->string('listing_level', ['fielddata' => true]);
            $map->string('city', ['index' => 'not_analyzed']);
            $map->string('category_primary', ['index' => 'not_analyzed']);
            $map->string('category_secondary', ['index' => 'not_analyzed']);
            $map->point('coordinates', ['lat_long' => true, 'geo_point' => true]);

        }, $this->getModelIndex());
    }
}
