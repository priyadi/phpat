<?php

namespace PHPatOld\Parser\Ast\Collector;

use PHPatOld\Parser\Ast\Classmap\Classmap;
use PHPatOld\Parser\Ast\FullClassName;
use PHPatOld\Parser\Ast\Traverser\TraverseContext;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassCollector extends NodeVisitorAbstract
{
    public function enterNode(Node $node)
    {
        /** @phpstan-ignore-next-line https://github.com/nikic/PHP-Parser/pull/822 */
        if ($node instanceof Node\Stmt\ClassLike && ($node->namespacedName ?? null) !== null) {
            $class = FullClassName::createFromFQCN($node->namespacedName->toString());

            TraverseContext::registerClass($class);
            Classmap::registerClass(
                $class,
                TraverseContext::pathname(),
                get_class($node),
                $this->retrieveFlags($node)
            );
        }

        return $node;
    }

    private function retrieveFlags(Node $node): ?int
    {
        $flags = ($node instanceof Node\Stmt\Class_) ? $node->flags : null;
        return ($flags === 0) ? null : $flags;
    }
}