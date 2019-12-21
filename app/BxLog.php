<?php

namespace App;

class BxLog
{
    public function log_debug( $message = "" )
    {
        if ( is_array( $message ) ) {
            echo 'Debug: ';
            print_r( $message );
            echo '</br>';
        } else {
            echo 'Debug: ' . $message . '</br>';
        }
    }

    public function log_info( $message = "" )
    {
        echo 'Info: ' . $message . '</br>';
    }

    public function log_warning( $message = "" )
    {
        echo 'Warning: ' . $message . '</br>';
    }
}
