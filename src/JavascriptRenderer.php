<?php namespace Barryvdh\Debugbar;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use Illuminate\Routing\UrlGenerator;

/**
 * {@inheritdoc}
 */
class JavascriptRenderer extends BaseJavascriptRenderer
{
    /** @var \Illuminate\Routing\UrlGenerator */
    protected $url;

    protected $cssVendors = array(
        // 'vendor/font-awesome/css/font-awesome.min.css',  // Removed until font is embedded
        'vendor/highlightjs/styles/github.css'
    );

    // Removed 'openhandler.js' until new release is tagged.
    protected $jsFiles = array('debugbar.js', 'widgets.js');

    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles[]   = __DIR__ . '/Resources/laravel-debugbar.css';
        $this->jsFiles[]    = __DIR__ . '/Resources/openhandler.js';
        $this->cssVendors[] = __DIR__ . '/Resources/vendor/font-awesome/style.css';
    }

    /**
     * Set the URL Generator
     *
     * @param \Illuminate\Routing\UrlGenerator $url
     */
    public function setUrlGenerator($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function renderHead()
    {
        if(!$this->url){
            return parent::renderHead();
        }

        $jsModified = $this->getModifiedTime('js');
        $cssModified = $this->getModifiedTime('css');

        $html = '';
        $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s?%s">' . "\n", $this->url->route('debugbar.assets.css'), $cssModified);
        $html .= sprintf('<script type="text/javascript" src="%s?%s"></script>' . "\n", $this->url->route('debugbar.assets.js'), $jsModified);

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= '<script type="text/javascript">jQuery.noConflict(true);</script>' . "\n";
        }

        return $html;
    }
    
    /**
     * Return assets as a string
     *
     * @param array $files
     * @return string
     */
    public function dumpAssetsToString($files)
    {
        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }
        return $content;
    }
    
    /**
     * Get the last modified time of any assets.
     * 
     * @param string $type 'js' or 'css'
     * @return int
     */
    protected function getModifiedTime($type)
    {
        $files = $this->getAssets($type);

        $latest = 0;
        foreach($files as $file){
            $mtime = filemtime($file);
            if($mtime > $latest){
                $latest = $mtime;
            }
        }
        return $latest;
    }

}
