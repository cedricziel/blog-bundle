<?php

namespace CedricZiel\BlogBundle\Controller;

use CedricZiel\BlogBundle\Entity\Post;
use CedricZiel\BlogBundle\Repository\PostRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller implements PaginatorAwareInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Sets the KnpPaginator instance.
     *
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route(name="cedricziel_blog_post_index", path="/articles")
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $blogPostsQuery = $this->postRepository->findAllPublishedOrderedByDateQuery();

        $pagination = $this->paginator->paginate(
            $blogPostsQuery,
            $request->query->getInt('page', 1),
            5
        );


        return [
            'blogPosts' => $pagination,
        ];
    }

    /**
     * @Cache(lastModified="blogPost.getLastModified()", ETag="blogPost.getId() ~ blogPost.getUpdatedAt().getTimestamp()~ blogPost.getCreatedAt().getTimestamp()")
     * @Route("/articles/{post_slug}")
     * @Route("/articles/{post_slug}/")
     * @ParamConverter("blogPost", options={"mapping": {"post_slug": "slug"}})
     * @Method("GET")
     * @Template()
     *
     * @param Post $blogPost
     *
     * @return array
     */
    public function showAction(Post $blogPost)
    {

        return [
            'blogPost' => $blogPost,
        ];
    }

    /**
     * @Route("/articles/{id}/preview")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     * @Template()
     *
     * @param Post $blogPost
     *
     * @return array
     */
    public function previewAction(Post $blogPost)
    {

        return [
            'blogPost' => $blogPost,
        ];
    }
}
