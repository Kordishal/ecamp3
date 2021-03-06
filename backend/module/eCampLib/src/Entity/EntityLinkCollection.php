<?php

namespace eCamp\Lib\Entity;

use Doctrine\Common\Collections\Selectable;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use Zend\Paginator\Paginator;

class EntityLinkCollection extends Paginator {
    public function __construct($adapter) {
        if ($adapter instanceof Selectable) {
            $adapter = new SelectableAdapter($adapter);
        }

        parent::__construct($adapter);
    }

    public function getIterator() {
        $items = parent::getIterator();

        foreach ($items as $item) {
            if ($item instanceof BaseEntity) {
                yield new EntityLink($item);
            } else {
                yield $item;
            }
        }
    }
}
