<?php
function loader($class)
{
    $class = str_replace('Inachis\\Component\\JiraIntegration\\', '', $class);
    $file = 'src/' . $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register('loader');
