<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BetterReflection\Reflection\ReflectionClass;

use function Safe\sprintf;

/**
 * A trait cannot change to become a interface, as that forces all implementations
 * that use it to change from `use` to `implements`
 */
final class TraitBecameInterface implements TraitBased
{
    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes
    {
        if ($toTrait->isTrait() || ! $toTrait->isInterface() || ! $fromTrait->isTrait()) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            sprintf('Interface %s became an interface', $fromTrait->getName()),
            true
        ));
    }
}
