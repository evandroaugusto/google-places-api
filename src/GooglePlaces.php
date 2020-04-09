<?php

namespace evandroaugusto\GooglePlaces;


class GooglePlaces
{
    const apiUrl = "https://maps.googleapis.com/maps/api/place";

    protected $apiKey;
    protected $outputType;
    protected $client;
    protected $entity;


    public function __construct($apiKey = false, $client = false)
    {
        if ($apiKey == false) {
            throw new \Exception('You must specify an API key.', 1);
        }

        $this->apiKey     = $apiKey;
        $this->outputType = 'json';
        $this->setClient($client);
    }

    /**
     * Get nearby places assembled with picture
     *
     * @param  array $params
     * @return array
     */
    public function nearbySearch($params)
    {
        // convert result to array
        $result = $this->get('nearbysearch', $params);
        $decode = json_decode($result);

        // get place photos
        foreach ($decode->results as $place) {
            if (isset($place->photos[0])) {
                $photoSrc = $this->get('photo', [
                    'photoreference' => $place->photos[0]->photo_reference,
                    'maxwidth'       => 450,
                    'maxheight'      => 300,
                ]);

                $place->photos[0]->src = $photoSrc;
            }
        }
                
        // encode in json structured
        return $decode;
    }

    /**
     * Get places by from a string search
     *
     * @param  array $params
     * @return array
     */
    public function textSearch($params)
    {
        // convert result to array
        $result = $this->get('textsearch', $params);
        $decode = json_decode($result);

        // get place photos
        foreach ($decode->results as $place) {
            if (isset($place->photos[0])) {
                $photoSrc = $this->get('photo', [
                    'photoreference' => $place->photos[0]->photo_reference,
                    'maxwidth'       => 450,
                    'maxheight'      => 300,
                ]);
                $place->photos[0]->src = $photoSrc;
            }
        }
                
        // encode in json structured
        return $decode;
    }

    /**
     * Get place details assembled with pictures
     *
     * @param  arry $params [description]
     * @return json
     */
    public function placeDetail($params)
    {
        $detail = $this->get('details', $params);
        $decode = json_decode($detail);

        $photos = [];
        $i = 0;
        
        if (isset($decode->result->photos)) {
            // get place photos (limit to 5)
            foreach ($decode->result->photos as $photo) {
                if ($i == 5) {
                    break;
                }
                $photoSrc = $this->get('photo', [
                    'photoreference' => $photo->photo_reference,
                    'maxwidth'       => 600,
                    'maxheight'      => 400,
                ]);

                $photo->src = $photoSrc;
                $photos[] = $photo;
                $i++;
            }
            $decode->result->photos = $photos;
        }

        //
        // generate link to static map
        //
        $location = array(
            $decode->result->geometry->location->lat,
            $decode->result->geometry->location->lng,
        );
        
        // prepare Google Static Map
        $staticMap = new GoogleStaticMap([
            'size'    => '600x300',
            'zoom'    => 16,
            'scale'   => 1,
            'markers' => implode(',', $location)
        ]);

        $decode->result->static_map = $staticMap->call();

        // encode in json structured
        return $decode;
    }

    /**
     * Raw query data from Google Places
     *
     * @param [type] $method [description]
     */
    public function get($method, $params)
    {
        switch ($method) {
            case 'nearbysearch':
            case 'textsearch':
                $this->entity = new GooglePlacesSearch($method, $params);
                break;

            case 'details':
                $this->entity = new GooglePlacesDetail($params);
                break;

            case 'photo':
                $this->entity = new GooglePlacesPhoto($params);
                break;

            default:
                throw new \Exception('Invalid method specified: ' . $method, 1);
                break;
        }

        return $this->call();
    }

    /**
     * Call
     * Execute method to query data from Google Places API
     *
     * @return void
     */
    protected function call()
    {
        // we just need the url to request photos
        if ($this->entity->getMethod() == 'photo') {
            $url = self::apiUrl . '/' . $this->entity->getMethod();

            return $url . '?' . $this->entity->buildQuery() . '&key=' . $this->apiKey;
        }

        // url to query data
        $url = self::apiUrl . '/' . $this->entity->getMethod() . '/' . $this->outputType;
        $url_request = $url . '?' . $this->entity->buildQuery() . '&key=' . $this->apiKey;

        return $this->client->get($url_request);
    }

    /**
     * Set client
     *
     * @param Object $client [description]
     */
    protected function setClient($client)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new GooglePlacesClient();
        }
    }
}
