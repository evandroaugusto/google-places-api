<?php

namespace evandroaugusto\GooglePlaces;


class GoogleStaticMap
{
    const apiUrl = 'https://maps.googleapis.com/maps/api/staticmap';

    // required
    protected $size;

    // optional (some fields become required based on conditions)
    protected $center;
    protected $zoom;
    protected $scale;
    protected $language;
    protected $markers;


    public function __construct($params)
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

        return $this;
    }


    /**
     * Build the querystring parameters
     *
     * @return string querystring creator
     */
    protected function parameterBuilder()
    {
        $queryString = false;

        $parameters = get_object_vars($this);
        $parameters = array_filter($parameters);
        $queryString = http_build_query($parameters);

        return $queryString;
    }

    /**
     * Validate the requirements
     *
     * @return  bool
     */
    public function validate()
    {
        if (!isset($this->size)) {
            throw new \Exception("You must specify the size of the static map", 1);
            return false;
        }
        if (!isset($this->markers)) {
            if (!isset($this->center) || !isset($this->zoom)) {
                throw new \Exception("If markers is not specified, you must set: center and zoom", 1);
                return false;
            }
        }
        if (isset($this->scale)) {
            if ($this->scale != 1 && $this->scale !=2) {
                throw new \Exception("Scale must be 1 or 2 if set", 1);
            }
        }

        return true;
    }

    /**
     * Build the url to call
     * @return [type] [description]
     */
    public function buildQuery()
    {
        if (!$this->validate()) {
            throw new \Exception('Problem on validate Static Map', 1);
        }

        return self::apiUrl . '?' . $this->parameterBuilder();
    }

    /**
     * Execute the function
     * @return void
     */
    public function call()
    {
        return $this->buildQuery();
    }
}
