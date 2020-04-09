<?php

namespace evandroaugusto\GooglePlaces;


class GooglePlacesDetail
{
    const method = 'details';
    protected $language = 'pt-BR';
    protected $placeId;


    public function __construct($params)
    {
        if ($params['placeid']) {
            $this->placeId = $params['placeid'];
        }
    }

    /**
     * Build the querystring parameters
     * @return string querystring creator
     */
    public function buildQuery()
    {
        if (!$this->validate()) {
            throw new \Exception('Problem on validate place detail', 1);
        }

        return $this->parameterBuilder();
    }

    /**
     * Validate the type of call method
     *
     * @return void
     */
    protected function validate()
    {
        if (empty($this->placeId)) {
            throw new \Exception('You must specify a place id.', 1);
            return false;
        }

        return true;
    }

    /**
     * Build the querystring parameters
     * @return string querystring creator
     */
    protected function parameterBuilder()
    {
        return 'placeid=' . $this->placeId . '&language=' . $this->language;
    }


    public function getMethod()
    {
        return self::method;
    }
}
