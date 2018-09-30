<?php

namespace CedricZiel\BlogBundle\Twig\Extension;

use CedricZiel\BlogBundle\Entity\Post;
use Spatie\SchemaOrg\Schema;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class BlogExtension extends \Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var UploaderHelper
     */
    private $vichHelper;

    public function __construct(UrlGeneratorInterface $router, UploaderHelper $vichHelper)
    {
        $this->router = $router;
        $this->vichHelper = $vichHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'blog';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('blog_schemaorg', [$this, 'blogSchemaOrgFunction']),
        ];
    }

    /**
     * Outputs schema.org markup
     *
     * @param Post $blog
     *
     * @return string
     */
    public function blogSchemaOrgFunction($blog)
    {
        if ($blog->getCreatedAt() === null) {
            $blog->setCreatedAt(new \DateTime());
        }

        $dateModified = $blog->getUpdatedAt();
        if ($dateModified === null) {
            $dateModified = $blog->getCreatedAt();
        }

        $publishedAt = $blog->getPublishedAt();
        if ($publishedAt === null) {
            $publishedAt = new \DateTime();
        }

        $blogPosting = Schema::blogPosting()
            ->identifier($this->router->generate('cedricziel_blog_post_show', ['post_slug' => $blog->getSlug()], RouterInterface::ABSOLUTE_URL))
            ->url($this->router->generate('cedricziel_blog_post_show', ['post_slug' => $blog->getSlug()], RouterInterface::ABSOLUTE_URL))
            ->name($blog->getTitle())
            ->author(Schema::person()->name('Cedric Ziel'))
            ->publisher(Schema::person()->name('Cedric Ziel'))
            ->datePublished($publishedAt->format('Y-m-d'))
            ->dateModified($dateModified->format('c'))
            ->headline($blog->getTitle())
            ->articleBody($blog->getBody());

        if ($blog->getTitleImage() !== null) {
            $path = $this->vichHelper->asset($blog, 'titleImageFile');
            $blogPosting->image(Schema::imageObject()->url($path));

        }

        return $blogPosting->toScript();
    }
}
