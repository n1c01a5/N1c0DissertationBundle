<?php

namespace N1c0\DissertationBundle;

final class Events
{
    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Dissertation.
     *
     * This event allows you to modify the data in the Dissertation prior
     * to persisting occuring. The listener receives a
     * N1c0\DissertationBundle\Event\DissertationPersistEvent instance.
     *
     * Persisting of the dissertation can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const DISSERTATION_PRE_PERSIST = 'n1c0_dissertation.dissertation.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Comment.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Comment to be persisted before performing
     * those actions. The listener receives a
     * FOS\CommentBundle\Event\CommentEvent instance.
     *
     * @var string
     */
    const DISSERTATION_POST_PERSIST = 'n1c0_dissertation.dissertation.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Comment.
     *
     * The listener receives a FOS\CommentBundle\Event\CommentEvent
     * instance.
     *
     * @var string
     */
    const DISSERTATION_CREATE = 'n1c0_dissertation.dissertation.create';
}
