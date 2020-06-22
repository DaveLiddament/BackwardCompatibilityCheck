<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassConstantBased;

use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

use function Safe\sprintf;

final class ClassConstantVisibilityReduced implements ClassConstantBased
{
    private const VISIBILITY_PRIVATE = 'private';

    private const VISIBILITY_PROTECTED = 'protected';

    private const VISIBILITY_PUBLIC = 'public';

    public function __invoke(ReflectionClassConstant $fromConstant, ReflectionClassConstant $toConstant): Changes
    {
        $visibilityFrom = $this->propertyVisibility($fromConstant);
        $visibilityTo   = $this->propertyVisibility($toConstant);

        // Works because private, protected and public are sortable:
        if ($visibilityFrom <= $visibilityTo) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            sprintf(
                'Constant %s::%s visibility reduced from %s to %s',
                $fromConstant->getDeclaringClass()->getName(),
                $fromConstant->getName(),
                $visibilityFrom,
                $visibilityTo
            ),
            true
        ));
    }

    private function propertyVisibility(ReflectionClassConstant $property): string
    {
        if ($property->isPublic()) {
            return self::VISIBILITY_PUBLIC;
        }

        if ($property->isProtected()) {
            return self::VISIBILITY_PROTECTED;
        }

        return self::VISIBILITY_PRIVATE;
    }
}
