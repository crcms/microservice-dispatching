<?php

$app = new \Illuminate\Container\Container();

\Illuminate\Container\Container::setInstance($app);

function app()
{
    return \Illuminate\Container\Container::getInstance();
}
