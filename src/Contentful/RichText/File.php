<?php

declare(strict_types=1);

namespace App\Contentful\RichText;

use Contentful\RichText\Node\AssetHyperlink;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\NodeRenderer\NodeRendererInterface;
use Contentful\RichText\RendererInterface;
use LogicException;

final class File implements NodeRendererInterface
{
    public function supports(NodeInterface $node): bool
    {
        return $node instanceof AssetHyperlink;
    }

    public function render(RendererInterface $renderer, NodeInterface $node, array $context = []): string
    {
        /** @var AssetHyperlink $node */
        if (!$this->supports($node)) {
            throw new LogicException(sprintf(
                'Trying to use node renderer "%s" to render unsupported node of class "%s".',
                __CLASS__,
                get_class($node)
            ));
        }

        return sprintf(
            '<a href="%s" title="%s" target="_blank">%s</a>',
            $node->getAsset()->jsonSerialize()['fields']->file->getUrl(),
            $node->getTitle(),
            $renderer->renderCollection($node->getContent(), $context)
        );
    }
}
