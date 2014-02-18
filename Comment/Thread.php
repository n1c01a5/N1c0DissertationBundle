<?php

namespace N1c0\DissertationBundle\Comment;

use Symfony\Component\HttpFoundation\RequestStack;

class Thread {
    protected $requestStack;
    private $appThread;
    private $appComment;

    public function __construct(RequestStack $requestStack, $appThread, $appComment)
    {
        $this->requestStack = $requestStack;
        $this->appThread = $appThread;
        $this->appComment    = $appComment;
    }

    public function getThread($id)
    {
        $thread = $this->appThread->findThreadById($id);
        $request = $this->requestStack->getCurrentRequest();
        
        if (null === $thread) {
            $thread = $this->appThread->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->appThread->saveThread($thread);
        }

        $comments = $this->appComment->findCommentTreeByThread($thread);

        return array(
            'comments' => $comments,
            'thread' => $thread,
        );
    }
}
