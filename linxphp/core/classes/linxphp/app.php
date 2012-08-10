<?php
namespace linxphp;

class app
{
    /**
     * it returns the phyphisical path where the application is hosted at.
     */
    public static function path()
    {
        # TODO: armar esto utilizando la configuracion.
        # Configuration::get
        if (isset($_SERVER['REDIRECT_SUBDOMAIN_DOCUMENT_ROOT']))
        $application_directory = dirname(realpath($_SERVER['REDIRECT_SUBDOMAIN_DOCUMENT_ROOT'].$_SERVER['PHP_SELF']));
        else
        $application_directory = dirname(realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']));

        return $application_directory.'/';
        return realpath(dirname(__FILE__).'/../../').'/';
    }

}
