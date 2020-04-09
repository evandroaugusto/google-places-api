<?php

namespace evandroaugusto\GooglePlaces;


class GooglePlacesPhoto
{
    const method = 'photo';

    protected $maxWidth;
    protected $maxHeight;
    protected $photoReference;


    public function __construct($params)
    {
        if (isset($params['photoreference'])) {
            $this->photoReference = $params['photoreference'];
        }

        if (isset($params['maxwidth'])) {
            $this->maxWidth = $params['maxwidth'];
        }

        if (isset($params['maxheight'])) {
            $this->maxheight = $params['maxheight'];
        }
    }

    /**
     * Validate the type of call method
     *
     * @return void
     */
    protected function validate()
    {
        if (!isset($this->photoReference)) {
            throw new \Exception('You must specify the reference id', 1);
        }

        if (!isset($this->maxWidth) && !isset($this->maxHeight)) {
            throw new \Exception('You must set at least width or height', 1);
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
        $queryString = array(
            'maxwidth'       => $this->maxWidth,
            'maxheight'      => $this->maxHeight,
            'photoreference' => $this->photoReference
        );

        $queryString = http_build_query($queryString);

        return $queryString;
    }

    /**
     * Build the querystring parameters
     * @return string querystring creator
     */
    public function buildQuery()
    {
        if (!$this->validate()) {
            throw new \Exception('Error on validation', 1);
        }

        return $this->parameterBuilder();
    }

    public function getMethod()
    {
        return self::method;
    }
}
