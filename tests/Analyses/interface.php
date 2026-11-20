<?php

namespace App\Contracts;

interface Repository extends Countable, \IteratorAggregate, Sub\Searchable
{
}
