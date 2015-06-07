<?php

namespace Dave14\library;

class CRSS
{
    private $feed;
	
	public function __construct(array $feedUrls)
    {
    	require_once(__DIR__ . '/../autoloader.php');
    	
    	
    	$feed = new \SimplePie();
    	
    	$feed->set_cache_location(__DIR__ . '/cache');
    	
    	
    	$feed->set_feed_url($feedUrls);
    	
    	
    	$feed->init();
    	
    	$feed->handle_content_type();
    	$this->feed = $feed;
    }
    
    public function printFeed() {
    	$feed = $this->feed;
    	
    	$html = "<div class='header'>
    	<h2><a href='{$feed->get_permalink()}'>{$feed->get_title()}</a></h2>
    	<p>{$feed->get_description()}</p>
    	</div>";
    	
    	foreach ($feed->get_items() as $item) {
    		$html .= "<div class='rss'>
    		<h2><a href='{$item->get_permalink()}'>{$item->get_title()}</a></h2>
    		<p>{$item->get_description()}</p>
    		<p class ='smaller'>Posted on {$item->get_date('j F Y ')}</p>
    		</div>";
    	}
    	
    	return $html;
    }
}