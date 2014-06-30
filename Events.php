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
     * persisted the Dissertation.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Dissertation to be persisted before performing
     * those actions. The listener receives a
     * N1c0\DissertationBundle\Event\DissertationEvent instance.
     *
     * @var string
     */
    const DISSERTATION_POST_PERSIST = 'n1c0_dissertation.dissertation.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Dissertation.
     *
     * The listener receives a N1c0\DissertationBundle\Event\DissertationEvent
     * instance.
     *
     * @var string
     */
    const DISSERTATION_CREATE = 'n1c0_dissertation.dissertation.create';


    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Introduction.
     *
     * This event allows you to modify the data in the Introduction prior
     * to persisting occuring. The listener receives a
     * N1c0\DissertationBundle\Event\IntroductionPersistEvent instance.
     *
     * Persisting of the introduction can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const INTRODUCTION_PRE_PERSIST = 'n1c0_dissertation.introduction.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Argument.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Introduction to be persisted before performing
     * those actions. The listener receives a
     * N1c0\DissertationBundle\Event\IntroductionEvent instance.
     *
     * @var string
     */
    const INTRODUCTION_POST_PERSIST = 'n1c0_dissertation.introduction.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Introduction.
     *
     * The listener receives a N1c0\DissertationBundle\Event\IntroductionEvent
     * instance.
     *
     * @var string
     */
    const INTRODUCTION_CREATE = 'n1c0_dissertation.introduction.create';

    
    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Part.
     *
     * This event allows you to modify the data in the Part prior
     * to persisting occuring. The listener receives a
     * N1c0\DissertationBundle\Event\PartPersistEvent instance.
     *
     * Persisting of the part can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const PART_PRE_PERSIST = 'n1c0_dissertation.part.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Part.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Argument to be persisted before performing
     * those actions. The listener receives a
     * N1c0\DissertationBundle\Event\PartEvent instance.
     *
     * @var string
     */
    const PART_POST_PERSIST = 'n1c0_dissertation.part.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Part.
     *
     * The listener receives a N1c0\DissertationBundle\Event\PartEvent
     * instance.
     *
     * @var string
     */
    const PART_CREATE = 'n1c0_dissertation.part.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Argument.
     *
     * This event allows you to modify the data in the Argument prior
     * to persisting occuring. The listener receives a
     * N1c0\DissertationBundle\Event\ArgumentPersistEvent instance.
     *
     * Persisting of the argument can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const ARGUMENT_PRE_PERSIST = 'n1c0_dissertation.argument.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Argument.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Argument to be persisted before performing
     * those actions. The listener receives a
     * N1c0\DissertationBundle\Event\ArgumentEvent instance.
     *
     * @var string
     */
    const ARGUMENT_POST_PERSIST = 'n1c0_dissertation.argument.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Argument.
     *
     * The listener receives a N1c0\DissertationBundle\Event\ArgumentEvent
     * instance.
     *
     * @var string
     */
    const ARGUMENT_CREATE = 'n1c0_dissertation.argument.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Conclusion.
     *
     * This event allows you to modify the data in the Conclusion prior
     * to persisting occuring. The listener receives a
     * N1c0\DissertationBundle\Event\ConclusionPersistEvent instance.
     *
     * Persisting of the conclusion can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const CONCLUSION_PRE_PERSIST = 'n1c0_dissertation.conclusion.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Conclusion.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Conclusion to be persisted before performing
     * those actions. The listener receives a
     * N1c0\DissertationBundle\Event\ConclusionEvent instance.
     *
     * @var string
     */
    const CONCLUSION_POST_PERSIST = 'n1c0_dissertation.conclusion.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Conclusion.
     *
     * The listener receives a N1c0\DissertationBundle\Event\ConclusionEvent
     * instance.
     *
     * @var string
     */
    const CONCLUSION_CREATE = 'n1c0_dissertation.conclusion.create';
}
