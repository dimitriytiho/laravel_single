<?php

// Данный класс даёт возможность объект класса использовать как массив.


namespace App\Libs;

class Collection implements \ArrayAccess, \Iterator
{
    use TCollection;
}
