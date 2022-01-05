<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    /**
     * @var MarkdownParserInterface
     */
    private $parser;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var bool
     */
    private $isDebug;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MarkdownParserInterface $parser,
                                CacheInterface          $cache,
                                bool                    $isDebug,
                                LoggerInterface         $logger)
    {
        $this->parser = $parser;
        $this->cache = $cache;
        $this->isDebug = $isDebug;
        $this->logger = $logger;
    }

    public function parse(string $string)
    {
        if ($this->isDebug) {
            $this->logger->info('je ne suis pas en cache');
            return $this->parser->transformMarkdown($string);
        }

        $this->logger->info('je suis en cache');
        return $this->cache->get('markdown_' . md5($string), function () use ($string) {
            return $this->parser->transformMarkdown($string);
        });
    }

}