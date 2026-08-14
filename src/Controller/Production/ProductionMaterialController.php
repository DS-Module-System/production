<?php

namespace App\Controller\Production;

use App\Controller\Core\CoreBaseController;
use App\Entity\Production\Production;
use App\Entity\Production\ProductionMaterial;
use App\Form\Production\ProductionMaterialForm;
use App\Form\Production\ProductionMaterialSearchForm;
use App\Service\Production\ProductionStockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/production-materials/{productionId}', name: 'production_material_')]
class ProductionMaterialController extends CoreBaseController
{
    protected string $entityClass = ProductionMaterial::class;
    protected string $formClass = ProductionMaterialForm::class;
    protected string $searchFormClass = ProductionMaterialSearchForm::class;
    protected string $moduleTemplateName = 'production_material';

    #[Route(path: '', name: 'list')]
    #[IsGranted('ROLE_PRODUCTION_MATERIAL_VIEW')] 
    public function list(Request $request, int $productionId): Response
    {
        $production = $this->em->getRepository(Production::class)->find($productionId);
        if (!$production) {
            throw $this->createNotFoundException('Production not found');
        }

        $this->additionalData['production'] = $production;
        $this->appendSearchFormData['productionId'] = $production->getId();

        return $this->baseList($request, $request->query->getInt('page', 1));
    }

    #[Route(path: '/create', name: 'create')]
    #[IsGranted('ROLE_PRODUCTION_MATERIAL_CREATE')] 
    public function create(Request $request, int $productionId, ProductionStockService $productionStockService): Response
    {
        $this->isModalRequest = true;
        
        $production = $this->em->getRepository(Production::class)->find($productionId);
        if (!$production) {
            throw $this->createNotFoundException('Production not found');
        }

        $this->additionalData['production'] = $production;
        $this->callbacks['setDefaultEntityData'] = function (ProductionMaterial $material, array $additionalData) {
            $material->setProduction($additionalData['production']);
            $material->setCreatedBy($this->getUser());
            return $material;
        };

        $this->callbacks['postCreateFlush'] = function ($entity) use ($productionStockService) {
            // Изписваме суровините от склада
            $productionStockService->removeMaterialsFromWarehouse($entity);
        };

        return $this->baseCreate($request);
    }

    #[Route(path: '/{id}/edit', name: 'edit')]
    #[IsGranted('ROLE_PRODUCTION_MATERIAL_EDIT')] 
    public function edit($id, Request $request, int $productionId): Response
    {
        $this->isModalRequest = true;
        
        $production = $this->em->getRepository(Production::class)->find($productionId);
        if (!$production) {
            throw $this->createNotFoundException('Production not found');
        }

        $this->additionalData['production'] = $production;

        $material = $this->em->getRepository(ProductionMaterial::class)->find($id);
        if (!$material || $material->getProduction()->getId() !== $production->getId()) {
            throw $this->createNotFoundException();
        }

        $this->callbacks['preEditPersist'] = function ($entity) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            return $entity;
        };

        return $this->baseEdit($request, $id);
    }

    #[Route(path: '/deletes', name: 'deletes')]
    #[IsGranted('ROLE_PRODUCTION_MATERIAL_DELETE')] 
    public function deletes(Request $request, int $productionId): Response
    {
        return $this->baseDeletes($request);
    }
} 