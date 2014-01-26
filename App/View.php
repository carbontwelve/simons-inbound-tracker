<?php namespace Carbontwelve\InboundTracker;

/**
 * Class View
 *
 * View-specific wrapper.
 * Limits the accessible scope available to templates.
 * @author Martin Samson
 * @author Simon Dann
 * @package Carbontwelve\ButtonBoard
 * @since 1.0.0
 * @link http://stackoverflow.com/a/14144286/1225977
 */
class View
{
    /**
     * Template Directory
     */
    protected $templateDirectory;

    /**
     * Initialize a new view context.
     */
    public function __construct($templateDirectory)
    {
        $this->templateDirectory = $templateDirectory;
    }

    /**
     * Safely escape/encode the provided data.
     */
    public function h($data)
    {
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render the template, returning it's content.
     * @param string $template , template file
     * @param array $data Data made available to the view.
     * @return string The rendered template.
     */
    public function render($template, Array $data)
    {

        $template = str_replace('.', DIRECTORY_SEPARATOR, $template);

        extract($data);
        ob_start();
        include($this->templateDirectory . $template . '.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
