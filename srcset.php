<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Grav;
use RocketTheme\Toolbox\Event\Event;

use \Grav\Common\Page\Medium\ImageMedium;

class SrcsetPlugin extends Plugin {

    public static function getSubscribedEvents() {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public static function getImageMagicActions() {
        
        $actions = [];

        foreach(ImageMedium::$magic_actions as $action) {
            $actions[$action] = $action;
        }

        return $actions;
    }

    public function onPluginsInitialized() {

        if ($this->isAdmin()) {
            return;
        }

        $this->enable([
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0]
        ]);
    }

    public function onMarkdownInitialized($e) {

        $page = $e['page'];
        $markdown = $e['markdown'];

        $images = $page->media()->images();

        $config = $this->config->get('plugins.srcset');

        $this->grav['assets']->addJs('http://afarkas.github.io/lazysizes/lazysizes.min.js');

        
        foreach($images as $image) {
            $this->grav['debugger']->addMessage($this->grav['page']);
            // $image->resize(500);
            // $image->derivatives([400,800,1000,1200])->sizes('80vw');
            // $image->derivatives([400,800,1000,1200]);
            // ->sizes('(min-width: 400px) 800px, 50vw');
        }

        $markdown->addInlineType('!', 'ImageExtended', 0);

        require_once __DIR__ . '/src/functions.php';

        $markdown->inlineImageExtended = inlineImageExtended($images, $config, $this->grav['debugger'])->bindTo($markdown, $markdown);        
                
    }
}
