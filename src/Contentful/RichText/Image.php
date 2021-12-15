<?php

declare(strict_types=1);

namespace App\Contentful\RichText;

use Contentful\Core\File\ImageFile;
use Contentful\RichText\Node\EmbeddedAssetBlock;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\NodeRenderer\NodeRendererInterface;
use Contentful\RichText\RendererInterface;
use LogicException;

final class Image implements NodeRendererInterface
{
    public function supports(NodeInterface $node): bool
    {
        return $node instanceof EmbeddedAssetBlock && $node->getAsset()->jsonSerialize()['fields']->file instanceof ImageFile;
    }

    public function render(RendererInterface $renderer, NodeInterface $node, array $context = []): string
    {
        /** @var EmbeddedAssetBlock $node */
        if (!$this->supports($node)) {
            throw new LogicException(sprintf(
                'Trying to use node renderer "%s" to render unsupported node of class "%s".',
                __CLASS__,
                get_class($node)
            ));
        }

        return sprintf(
            '<p><img class="responsive" src="%s?fm=webp&q=80" loading="lazy" alt /></p>',
            $node->getAsset()->jsonSerialize()['fields']->file->getUrl()
        );
    }
}
