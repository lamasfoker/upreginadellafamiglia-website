<?php
declare(strict_types=1);

namespace App\Contentful;

use Contentful\Delivery\Query;

final class QueryFactory
{
    public function create(): Query
    {
        return new Query();
    }
}