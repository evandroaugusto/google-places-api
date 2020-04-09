<?php

namespace evandroaugusto\GooglePlaces;


class GooglePlacesSearch
{
    // required
    protected $method;
    protected $location;
    protected $radius;
    protected $rankby;

    // optional
    protected $keyword;
    protected $language;
    protected $minprice;
    protected $maxprice;
    protected $name;
    protected $opennow;
    protected $types;
    protected $type;
    protected $query;
    protected $pagetoken;


    public function __construct($method, $params = array())
    {
        if (!empty($params)) {
            $class_attr = get_object_vars($this);
            $attributes = array_intersect_key($params, $class_attr);
    
            foreach ($attributes as $key => $value) {
                $this->$key = $params[$key];
            }
        }

        // set default language
        if (!isset($this->language)) {
            $this->language = 'pt-BR';
        }

        $this->method = $method;
    }

    /**
     * Build the url to call
     * @return [type] [description]
     */
    public function buildQuery()
    {
        if (!$this->validate()) {
            throw new \Exception('Problem on validate search');
        }

        return $this->parameterBuilder();
    }

    /**
     * Build the querystring parameters
     * @return string querystring creator
     */
    protected function parameterBuilder()
    {
        $queryString = false;

        $parameters = get_object_vars($this);
        unset($parameters['method']);

        // Remove types when using keywords
        if (isset($parameters['keyword'])) {
            unset($parameters['types']);
            unset($parameters['type']);
        }

        $parameters = array_filter($parameters);

        $queryString = http_build_query($parameters);

        return $queryString;
    }

    /**
     * Validate the type of call method
     *
     * @return void
     */
    protected function validate()
    {
        if ($this->method == 'nearbysearch') {
            return $this->validateNearbySearch();
        }

        if ($this->method == 'textsearch') {
            return true;
        }

        return false;
    }



    /**
     * Validate the method nearBySearch
     * @return void
     */
    private function validateNearbySearch()
    {
        if (!isset($this->location)) {
            throw new \Exception('You must set a location', 1);
        }

        if (isset($this->rankby)) {
            switch ($this->rankby) {
                case 'distance':
                    $has_attr = (!isset($this->keyword) && !isset($this->name) && 
                        !isset($this->types) && !isset($this->type));

                    if ($has_attr) {
                        throw new \Exception('You must specify at least: keyword, name, types');
                    }

                    if (isset($this->radius)) {
                        unset($this->radius);
                    }
                    break;


                case 'prominence':
                    if (!isset($this->radius)) {
                        throw new \Exception('You must specify a radius');
                    }
                    if ($this->radius < 1) {
                        throw new \Exception('Radius must be at least 1');
                    }
                    break;
          }
        } else {
            throw new \Exception('You must specify the rankby');
        }

        return true;
    }

    /**
     * Validate text search
     * @return [type] [description]
     */
    public function validateTextSearch()
    {
        if (!isset($this->query)) {
            throw new \Exception('You must set a text to search');
        }

        return true;
    }

    /**
     * Get current method used
     * @return string 	method used
     */
    public function getMethod()
    {
        return $this->method;
    }
}
