<?php

namespace App\Enum\Production;

use App\Trait\Core\EnumLabelTrait;

enum ProductionMovementType: string
{
    use EnumLabelTrait;

    case IN = 'in';
    case OUT = 'out';

    public function getLabel(): string
    {
        return match($this) {
            self::IN => 'movementTypeIn',
            self::OUT => 'movementTypeOut',
        };
    }

    private function getDomain(): ?string
    {
        return 'production';
    }
} 