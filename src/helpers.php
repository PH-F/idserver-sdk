<?php

if (!function_exists('ids')) {
    /**
     * Get the IDServer Manager instance.
     *
     * @return \Xingo\IDServer\Manager
     */
    function ids()
    {
        return app('idserver.manager');
    }
}
