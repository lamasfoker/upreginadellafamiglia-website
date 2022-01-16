<?php

declare(strict_types=1);

namespace App\Contentful\RichText;

use App\Exception\ValidatorException;
use Contentful\Delivery\Resource\Entry;
use Contentful\RichText\Node\EmbeddedEntryBlock;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\NodeRenderer\NodeRendererInterface;
use Contentful\RichText\RendererInterface;
use LogicException;

final class EmbeddedVideo implements NodeRendererInterface
{
    public const EMBEDDED_VIDEO_CONTENT_TYPE_ID = 'embeddedVideo';

    public function supports(NodeInterface $node): bool
    {
        if (!$node instanceof EmbeddedEntryBlock) {
            return false;
        }
        /** @var Entry $entry */
        $entry = $node->getEntry();
        return $entry->getContentType()->getId() === self::EMBEDDED_VIDEO_CONTENT_TYPE_ID;
    }

    public function render(RendererInterface $renderer, NodeInterface $node, array $context = []): string
    {
        if (!$this->supports($node)) {
            throw new LogicException(
                sprintf(
                    'Trying to use node renderer "%s" to render unsupported node of class "%s".',
                    __CLASS__,
                    get_class($node)
                )
            );
        }
        /** @var EmbeddedEntryBlock $node */
        /** @var Entry $entry */
        $entry = $node->getEntry();
        $url = $this->getVideoUrl($entry->get('videoUrl'));

        return "<div class=\"xt-media-container bg-gray-100 pb-[56%] mb-4\"><iframe class=\"xt-media border-0\" src=\"$url\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen ></iframe></div>";
    }

    private function getVideoUrl(string $youtubeUrl): string
    {
        // see this stackoverflow https://stackoverflow.com/a/8260383
        $regExp = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/';
        if (!preg_match($regExp, $youtubeUrl, $match) || !is_array($match) || !array_key_exists(7, $match)) {
            throw new ValidatorException();
        }

        return "//www.youtube.com/embed/$match[7]";
    }
}
