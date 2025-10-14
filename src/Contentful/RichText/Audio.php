<?php

declare(strict_types=1);

namespace App\Contentful\RichText;

use Contentful\RichText\Node\AssetHyperlink;
use Contentful\RichText\Node\EmbeddedAssetBlock;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\NodeRenderer\NodeRendererInterface;
use Contentful\RichText\RendererInterface;
use LogicException;

final class Audio implements NodeRendererInterface
{
    public const CONTENT_TYPE_ID = 'audio/mpeg';

    public function supports(NodeInterface $node): bool
    {
        return $node instanceof EmbeddedAssetBlock && $node->getAsset()->jsonSerialize()['fields']->file->getContentType() === self::CONTENT_TYPE_ID;
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
            '<audio controls class="w-full"><source src="%s" type="audio/mp3" />Il tuo browser non supporta l\'elemento audio.</audio>',
            $node->getAsset()->jsonSerialize()['fields']->file->getUrl(),
        );
    }
}
