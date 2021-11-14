<?php

declare(strict_types=1);

namespace Zantolov\ZoogleCms\Bridge\Symfony\Controller;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ImageController extends AbstractController
{
    /**
     * @Route("/z/image/{filename}", name="zoogle_cms_local_image")
     */
    public function __invoke(string $filename, CacheItemPoolInterface $cache): Response
    {
        $content = $cache->getItem($filename)->get();
        if (empty($content)) {
            throw new NotFoundHttpException();
        }

        $response = new Response($content);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image');
        $response->setExpires(new \DateTimeImmutable('+1 year'));

        return $response;
    }
}
