<?php

function make($className, $attributes = [], $num = 1)
{
    return $num === 1 ? 
                factory($className, $num)->make($attributes)->first() :
                factory($className, $num)->make($attributes);
}

function create($className, $attributes = [], $num = 1)
{
    return $num === 1 ?
                factory($className, $num)->create($attributes)->first() :
                factory($className, $num)->create($attributes);
}
