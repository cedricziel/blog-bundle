<?php

namespace CedricZiel\BlogBundle\Controller;

use CedricZiel\BlogBundle\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Outputs an atom feed
 */
class FeedController extends Controller
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Method("GET")
     * @Route("/feed.{_format}", defaults={"_format": "rss"})
     * @Template()
     */
    public function feedAction()
    {
        $items = $this->postRepository->findAllPublishedOrderedByDateQuery()->getResult();

        return [
            'items' => $items,
        ];
    }
}
