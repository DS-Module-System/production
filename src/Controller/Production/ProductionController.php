<?php

namespace App\Controller\Production;

use App\Controller\Core\CoreBaseController;
use App\Entity\Production\Production;
use App\Form\Production\ProductionForm;
use App\Form\Production\ProductionSearchForm;
use App\Service\Production\ProductionStockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/production', name: 'production_')]
class ProductionController extends CoreBaseController
{
    protected string $entityClass = Production::class;
    protected string $formClass = ProductionForm::class;
    protected string $searchFormClass = ProductionSearchForm::class;
    protected string $moduleTemplateName = 'production';

    #[Route(path: '', name: 'list')]
    #[IsGranted('ROLE_PRODUCTION_VIEW')] 
    public function list(Request $request): Response
    {
        return $this->baseList($request, $request->query->getInt('page', 1));
    }

    #[Route(path: '/create', name: 'create')]
    #[IsGranted('ROLE_PRODUCTION_CREATE')] 
    public function create(Request $request, ProductionStockService $productionStockService): Response
    {
        $this->callbacks['preCreatePersist'] = function ($entity) {
            $entity->setCreatedBy($this->getUser());
            return $entity;
        };

        $this->callbacks['postCreateFlush'] = function ($entity) use ($productionStockService) {
            // Заприходваме продукцията в склада
            $productionStockService->addProductionToWarehouse($entity);
        };

        return $this->baseCreate($request);
    }

    #[Route(path: '/{id}/edit', name: 'edit')]
    #[IsGranted('ROLE_PRODUCTION_EDIT')] 
    public function edit($id, Request $request): Response
    {
        $this->callbacks['preEditPersist'] = function ($entity) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
            return $entity;
        };

        return $this->baseEdit($request, $id);
    }

    #[Route(path: '/deletes', name: 'deletes')]
    #[IsGranted('ROLE_PRODUCTION_DELETE')] 
    public function deletes(Request $request): Response
    {
        return $this->baseDeletes($request);
    }
} 