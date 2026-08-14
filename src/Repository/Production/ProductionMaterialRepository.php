<?php

namespace App\Repository\Production;

use App\Entity\Production\ProductionMaterial;
use App\Repository\Core\CoreRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ProductionMaterialRepository extends ServiceEntityRepository implements CoreRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionMaterial::class);
    }

    public function getPaginatedQuery(array $searchFormData = []): Query
    {
        $qb = $this->createQueryBuilder('pm')
            ->leftJoin('pm.production', 'p')
            ->leftJoin('pm.material', 'm')
            ->addSelect('p', 'm');

        // Търсене по productionId
        if (!empty($searchFormData['productionId'])) {
            $qb->andWhere('p.id = :productionId')
               ->setParameter('productionId', $searchFormData['productionId']);
        }

        // Търсене по материал
        if (!empty($searchFormData['material'])) {
            $qb->andWhere('m.name LIKE :material')
               ->setParameter('material', '%' . $searchFormData['material'] . '%');
        }

        // Сортиране по дата на създаване (най-нови първи)
        $qb->orderBy('pm.createdAt', 'DESC');

        return $qb->getQuery();
    }
} 