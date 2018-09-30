<?php

namespace CedricZiel\BlogBundle\EventListener;

use CedricZiel\BlogBundle\Entity\Post;
use Cocur\Slugify\Slugify;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PostSlugListener implements EventSubscriberInterface
{
    private $slugger;

    public function __construct(Slugify $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_persist' => array('setBlogPostSlug'),
        );
    }

    public function setBlogPostSlug(GenericEvent $event): void
    {
        $entity = $event->getSubject();

        if (!($entity instanceof Post)) {
            return;
        }

        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug);

        $event['entity'] = $entity;
    }
}
