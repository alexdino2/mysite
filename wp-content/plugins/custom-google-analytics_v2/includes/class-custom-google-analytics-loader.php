<?php
class Custom_Google_Analytics_Loader {
 
    protected $actions;
    protected $filters;
 
    public function __construct() {
 
        $this->actions = array();
        $this->filters = array();
     
    }
 
    public function add_action( $hook, $component, $callback, $args=0 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $args );
    }
 
    public function add_filter( $hook, $component, $callback, $args=0 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $args );
    }
 
    private function add( $hooks, $hook, $component, $callback, $args=0 ) {
        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback,
            'args'		=> $args
        );
        return $hooks;
    }
 
    public function run() {
        foreach ( $this->filters as $hook ) {
			if($hook['args'] != 0 )
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'], $hook['args'] ) );
			else
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
        foreach ( $this->actions as $hook ) {
			if($hook['args'] != 0 )
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'], $hook['args'] ) );
            else
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
    }
}
