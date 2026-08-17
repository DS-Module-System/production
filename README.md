# Production

Производствени операции: изходна продукция и вложени материали със складови движения IN/OUT.

## Функционалност

- CRUD на производства (продукт, количество, склад)
- CRUD на материали към производство
- Запис на складови движения (`ProductionStockMovement`)
- Услуги за потребление на материали и заприхождаване на готова продукция

## Интеграция в системата

Copy-in модул: файловете се копират в хоста под `App\`.

- Пътища: `src/Controller|Entity|Enum|Form|Repository|Service/Production/`, `templates/production/`, `templates/production_material/`, `translations/production.*.yaml`, `config/roles/production.yaml`
- Меню: Производство (`production_list`) при `ROLE_PRODUCTION_VIEW`
- Роли: `ROLE_PRODUCTION_{VIEW,CREATE,EDIT,DELETE}`, `ROLE_PRODUCTION_MATERIAL_{VIEW,CREATE,EDIT,DELETE}`
- Маршрути: `/production`, `/production-materials/{productionId}`

## Структура

- `ProductionController`, `ProductionMaterialController`
- Ентитети: `Production`, `ProductionMaterial`, `ProductionStockMovement`
- `ProductionService`, `ProductionStockService`
- Enum: `ProductionMovementType` (IN/OUT)

## Зависимости

- **erp-core**
- **product**
- **warehouse**

## Документация

- [docs/production/README.md](docs/production/README.md)
