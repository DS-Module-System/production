<?php

namespace App\Service\Production;

use App\Entity\Production\Production;
use App\Entity\Production\ProductionMaterial;
use App\Entity\Warehouse\WarehouseStock;
use App\Repository\Warehouse\WarehouseStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductionStockService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WarehouseStockRepository $warehouseStockRepository
    ) {
    }

    /**
     * Заприходва произведената продукция в склада
     */
    public function addProductionToWarehouse(Production $production): void
    {
        if (!$production->getWarehouse() || !$production->getProduct()) {
            return;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $production->getWarehouse(),
            'product' => $production->getProduct()
        ]);

        if (!$warehouseStock) {
            // Създаваме нов запис за складови наличности
            $warehouseStock = new WarehouseStock();
            $warehouseStock->setWarehouse($production->getWarehouse());
            $warehouseStock->setProduct($production->getProduct());
            $warehouseStock->setQuantity('0.00');
            $warehouseStock->setCreatedBy($production->getCreatedBy());
            $warehouseStock->setCreatedAt(new \DateTimeImmutable());
        }

        // Добавяме произведеното количество
        $currentQuantity = (float) $warehouseStock->getQuantity();
        $productionQuantity = (float) $production->getQuantity();
        $newQuantity = $currentQuantity + $productionQuantity;
        
        $warehouseStock->setQuantity((string) $newQuantity);
        $warehouseStock->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($warehouseStock);
        $this->entityManager->flush();
    }

    /**
     * Изписва суровини от склада за производство
     */
    public function removeMaterialsFromWarehouse(ProductionMaterial $material): void
    {
        if (!$material->getWarehouse() || !$material->getMaterial()) {
            return;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $material->getWarehouse(),
            'product' => $material->getMaterial()
        ]);

        if (!$warehouseStock) {
            throw new \Exception('Няма наличности от този материал в избрания склад');
        }

        $currentQuantity = (float) $warehouseStock->getQuantity();
        $requiredQuantity = (float) $material->getQuantity();

        if ($currentQuantity < $requiredQuantity) {
            throw new \Exception('Недостатъчни наличности от материал в склада');
        }

        // Изписваме използваното количество
        $newQuantity = $currentQuantity - $requiredQuantity;
        $warehouseStock->setQuantity((string) $newQuantity);
        $warehouseStock->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($warehouseStock);
        $this->entityManager->flush();
    }

    /**
     * Проверява дали има достатъчно наличности от материал в склада
     */
    public function checkMaterialAvailability(ProductionMaterial $material): bool
    {
        if (!$material->getWarehouse() || !$material->getMaterial()) {
            return false;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $material->getWarehouse(),
            'product' => $material->getMaterial()
        ]);

        if (!$warehouseStock) {
            return false;
        }

        $currentQuantity = (float) $warehouseStock->getQuantity();
        $requiredQuantity = (float) $material->getQuantity();

        return $currentQuantity >= $requiredQuantity;
    }

    /**
     * Връща наличностите от материал в склада
     */
    public function getMaterialStock(ProductionMaterial $material): float
    {
        if (!$material->getWarehouse() || !$material->getMaterial()) {
            return 0.0;
        }

        $warehouseStock = $this->warehouseStockRepository->findOneBy([
            'warehouse' => $material->getWarehouse(),
            'product' => $material->getMaterial()
        ]);

        if (!$warehouseStock) {
            return 0.0;
        }

        return (float) $warehouseStock->getQuantity();
    }
} 