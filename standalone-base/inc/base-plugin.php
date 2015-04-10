<?php

abstract class Base_Plugin {
    const ENABLED       = true;
    const DISABLED      = false;
    const EXCEPTION_HDR = 'PHP Exception:  ';

    private static $instance;

    protected static $activated = false;

    public static $class_name;
    public static $exception_msg_hdr;
    public static $exception_msg_dvdr;

    protected function __construct() {}

    private function __clone() {}

    public function activation_actions() {}

    protected function deactivation_actions() {}

//    private function __wakeup() {}

    public static function init() {}

    public function __activator() {
        if (! self::$activated) {
            self::$activated = true;
            $this->activation_actions();
        }
    }

    public function __deactivator() {
        if (self::$activated) {
            $this->deactivation_actions();
        }
    }

    public function __call( $method_name, $arguments ) {
        self::$exception_msg_hdr = 'Unknown method: ';
        self::$exception_msg_dvdr = '->';
        self::call_exception_handler( $method_name, $arguments );
    }

    public function set_class_name( $class = null ) {
        self::$class_name = get_class($class);

    }

    public static function __callStatic( $method_name, $arguments ) {
        self::$exception_msg_hdr = 'Unknown static method: ';
        self::$exception_msg_dvdr = '::';
        self::call_exception_handler( $method_name, $arguments);
    }

    public static function get_class_name() {
        if ( self::$class_name ) {
            return( self::$class_name );
        }

        return( get_class() );
    }

    public static function get_exception_msg( $method_name, $arguments ) {
        $exception_msg  = self::$exception_msg_hdr;
        $exception_msg .= self::get_class_name() . self::$exception_msg_dvdr . $method_name;
        $exception_msg .= ' with these arguments: ';
        $exception_msg .= implode( ', ', $arguments );
        $exception_msg .= PHP_EOL;
        return( $exception_msg );
    }

    public static function call_exception_handler( $method_name, $arguments ) {
        self::throw_exception_exception( self::get_exception_msg( $method_name, $arguments ) );
    }

    public static function throw_exception_exception( $exception_msg ) {
        error_log(self::EXCEPTION_HDR . $exception_msg, 0 );
    }

    public static function get_instance() {
        self::$instance = null;

        if ( self::$instance === null ) {
            self::$instance = new static();
        }

        return( self::$instance );
    }
}
