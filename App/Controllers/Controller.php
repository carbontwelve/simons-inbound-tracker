<?php namespace Carbontwelve\InboundTracker\Controllers;

class Controller
{

    /**
     * Local Instance of the App class
     * @var \Carbontwelve\InboundTracker\App  */
    protected $app;

    /**
     * Messages that are displayed to the views
     * @var array */
    protected $flashMessages = array(
        'success' => false,
        'error'   => false,
        'errors'  => array(),
        'inputs'  => array()
    );

    /**
     * @param \Carbontwelve\InboundTracker\App $app
     *
     */
    public function __construct ( \Carbontwelve\InboundTracker\App $app )
    {
        $this->app = $app;
    }

    /**
     * Combine both POST and GET data and "sanitize" it before adding to the
     * inputs array. Models should use validation on input to be safe.
     * @return array
     */
    protected function sanitizeInputs ()
    {
        $input = array_merge($_POST, $_GET);
        unset($input['submit']); // we don't need submit buttons data :)

        foreach ($input as &$value) {
            $value = trim(strip_tags($value));
        }

        return $input;
    }

    /**
     * Get an input by key
     * @param string $key
     * @param bool|string $default
     */
    protected function getInput ($key, $default = null)
    {
        if (isset($this->flashMessages['inputs'][$key])){ return $this->flashMessages['inputs'][$key]; }else{ return $default; }
    }

    /**
     * Set an input by key
     * @param $key
     * @param $value
     */
    protected function setInput ($key, $value)
    {
        $this->flashMessages['inputs'][$key] = $value;
    }
}
