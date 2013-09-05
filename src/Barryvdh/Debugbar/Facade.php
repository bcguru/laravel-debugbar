<?php namespace Barryvdh\Debugbar;

class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'debugbar'; }

    /**
     * Resolve a collector
     *
     * @param $name
     * @return mixed
     */
    public static function make($name){

        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        return $instance[$name];
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public static function startMeasure($name, $label=null){
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        $time->startMeasure($name, $label);
    }

    /**
     * Stops a measure
     *
     * @param string $name
     */
    public static function stopMeasure($name)
    {
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        $time->stopMeasure($name);
    }

    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure|callable $closure
     */
    public static function measure($label, \Closure $closure)
    {
        /** @var \DebugBar\DataCollector\TimeDataCollector $time */
        $time = static::make('time');
        $time->measure($label, $closure);
    }

    /**
     * Override static calls for adding messages
     *
     * @param string $method
     * @param array $args
     * @return mixed|void
     */
    public static function __callStatic($method, $args)
    {
        $messageLevels = array('emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug', 'log');
        if(in_array($method, $messageLevels)){
            /** @var \DebugBar\DataCollector\MessagesCollector $message */
            $message = static::make('messages');
            $message->addMessage($args[0], $method);
        }else{
            parent::__callStatic($method, $args);
        }
    }


}