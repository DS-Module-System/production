<?php

namespace App\Repository\Production;

use App\Entity\Production\Production;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ProductionRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Production::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.product', 'prod')
            ->leftJoin('p.warehouse', 'w')
            ->addSelect('prod', 'w');

        // Търсене по име
        if (!empty($searchFormData['name'])) {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $searchFormData['name'] . '%');
        }

        // Търсене по продукт
        if (!empty($searchFormData['product'])) {
            $qb->andWhere('prod.id LIKE :product')
               ->setParameter('product', $searchFormData['product']);
        }

        // Търсене по начална дата
        if (!empty($searchFormData['startDateFrom'])) {
            $qb->andWhere('p.startDate >= :startDateFrom')
               ->setParameter('startDateFrom', $searchFormData['startDateFrom']);
        }

        if (!empty($searchFormData['startDateTo'])) {
            $qb->andWhere('p.startDate <= :startDateTo')
               ->setParameter('startDateTo', $searchFormData['startDateTo']);
        }

        // Търсене по крайна дата
        if (!empty($searchFormData['endDateFrom'])) {
            $qb->andWhere('p.endDate >= :endDateFrom')
               ->setParameter('endDateFrom', $searchFormData['endDateFrom']);
        }

        if (!empty($searchFormData['endDateTo'])) {
            $qb->andWhere('p.endDate <= :endDateTo')
               ->setParameter('endDateTo', $searchFormData['endDateTo']);
        }

        // Търсене по склад
        if (!empty($searchFormData['warehouse'])) {
            $qb->andWhere('w.id = :warehouse')
               ->setParameter('warehouse', $searchFormData['warehouse']);
        }

        // Сортиране по дата на създаване (най-нови първи)
        $qb->orderBy('p.createdAt', 'DESC');

        return $qb->getQuery();
    }
} 