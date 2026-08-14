<?php

namespace App\Service\Production;

use App\Entity\Production\Production;
use App\Entity\Production\ProductionMaterial;
use App\Entity\Production\ProductionStockMovement;
use App\Entity\Warehouse\WarehouseStock;
use App\Enum\Production\ProductionMovementType;
use App\Repository\Warehouse\WarehouseStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WarehouseStockRepository $warehouseStockRepository
    ) {
    }

    /**
     * Регистрира изписване на материали от склад за производство
     */
    public function registerMaterialConsumption(
        Production $production,
        ProductionMaterial $material,
        WarehouseStock $warehouseStock,
        string $quantity,
        ?string $notes = null
    ): ProductionStockMovement {
        // Създаваме движение за изписване
        $stockMovement = new ProductionStockMovement();
        $stockMovement->setProduction($production);
        $stockMovement->setProduct($material->getMaterial());
        $stockMovement->setWarehouse($warehouseStock->getWarehouse());
        $stockMovement->setQuantity($quantity);
        $stockMovement->setMovementType(ProductionMovementType::OUT);
        $stockMovement->setNotes($notes);
        $stockMovement->setCreatedBy($production->getCreatedBy());

        // Намаляваме количеството в склада
        $currentQuantity = (float) $warehouseStock->getQuantity();
        $consumedQuantity = (float) $quantity;
        $newQuantity = $currentQuantity - $consumedQuantity;
        
        if ($newQuantity < 0) {
            throw new \Exception('Недостатъчно количество в склада');
        }

        $warehouseStock->setQuantity((string) $newQuantity);

        $this->entityManager->persist($stockMovement);
        $this->entityManager->persist($warehouseStock);
        $this->entityManager->flush();

        return $stockMovement;
    }

    /**
     * Регистрира вписване на готов продукт в склад
     */
    public function registerProductOutput(
        Production $production,
        WarehouseStock $warehouseStock,
        string $quantity,
        ?string $notes = null
    ): ProductionStockMovement {
        // Създаваме движение за вписване
        $stockMovement = new ProductionStockMovement();
        $stockMovement->setProduction($production);
        $stockMovement->setProduct($production->getProduct());
        $stockMovement->setWarehouse($warehouseStock->getWarehouse());
        $stockMovement->setQuantity($quantity);
        $stockMovement->setMovementType(ProductionMovementType::IN);
        $stockMovement->setNotes($notes);
        $stockMovement->setCreatedBy($production->getCreatedBy());

        // Увеличаваме количеството в склада
        $currentQuantity = (float) $warehouseStock->getQuantity();
        $producedQuantity = (float) $quantity;
        $newQuantity = $currentQuantity + $producedQuantity;

        $warehouseStock->setQuantity((string) $newQuantity);

        $this->entityManager->persist($stockMovement);
        $this->entityManager->persist($warehouseStock);
        $this->entityManager->flush();

        return $stockMovement;
    }

    /**
     * Връща общото количество използвани материали за производство
     */
    public function getTotalMaterialConsumption(Production $production): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('pm.material as material, SUM(pm.quantity) as totalQuantity')
           ->from(ProductionMaterial::class, 'pm')
           ->where('pm.production = :production')
           ->setParameter('production', $production)
           ->groupBy('pm.material');

        return $qb->getQuery()->getResult();
    }

    /**
     * Връща общото количество произведен продукт
     */
    public function getTotalProductOutput(Production $production): ?string
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('SUM(psm.quantity) as totalQuantity')
           ->from(ProductionStockMovement::class, 'psm')
           ->where('psm.production = :production')
           ->andWhere('psm.movementType = :movementType')
           ->setParameter('production', $production)
           ->setParameter('movementType', ProductionMovementType::IN);

        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?: '0.00';
    }
} 