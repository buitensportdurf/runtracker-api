<?php


namespace App\Collection;


use Knp\Component\Pager\Pagination\PaginationInterface;

class PaginatedCollection
{
    private $total;

    private $count;

    private $items;

    public function __construct(PaginationInterface $pagination)
    {
        $this->items = $pagination->getItems();
        $this->total = $pagination->getTotalItemCount();
        $this->count = $pagination->count();
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->items;
    }
}