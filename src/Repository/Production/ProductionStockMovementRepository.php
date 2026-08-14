<?php

namespace App\Repository\Production;

use App\Entity\Production\ProductionStockMovement;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ProductionStockMovementRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionStockMovement::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('psm')
            ->leftJoin('psm.production', 'p')
            ->leftJoin('psm.product', 'prod')
            ->leftJoin('psm.warehouse', 'w')
            ->addSelect('p', 'prod', 'w');

        // Търсене по производство
        if (!empty($searchFormData['production'])) {
            $qb->andWhere('p.name LIKE :production')
               ->setParameter('production', '%' . $searchFormData['production'] . '%');
        }

        // Търсене по продукт
        if (!empty($searchFormData['product'])) {
            $qb->andWhere('prod.name LIKE :product')
               ->setParameter('product', '%' . $searchFormData['product'] . '%');
        }

        // Търсене по склад
        if (!empty($searchFormData['warehouse'])) {
            $qb->andWhere('w.name LIKE :warehouse')
               ->setParameter('warehouse', '%' . $searchFormData['warehouse'] . '%');
        }

        // Търсене по тип движение
        if (!empty($searchFormData['movementType'])) {
            $qb->andWhere('psm.movementType = :movementType')
               ->setParameter('movementType', $searchFormData['movementType']);
        }

        // Сортиране по дата на създаване (най-нови първи)
        $qb->orderBy('psm.createdAt', 'DESC');

        return $qb->getQuery();
    }
} 