<?php

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function __construct(
        private readonly PaginatorInterface $paginator
    ) {
    }

    public function paginate(
        mixed $target,
        Request $request,
        int $limit = 10,
        string $pageParameterName = 'page'
    ): PaginationInterface {
        return $this->paginator->paginate(
            $target,
            $request->query->getInt($pageParameterName, 1),
            $limit
        );
    }
}