<?php

namespace N1c0\DissertationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use N1c0\DissertationBundle\Exception\InvalidFormException;
use N1c0\DissertationBundle\Form\DissertationType;
use N1c0\DissertationBundle\Model\DissertationInterface;

class LogController extends FOSRestController
{
    /**
     * Gets logs.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets logs for a given id",
     *   output = "Gedmo\Loggable\Entity\LogEntry", 
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the dissertation is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="logsDissertation")
     *
     * @param int                   $id                   the dissertation id
     *
     * @return array
     *
     * @throws NotFoundHttpException when introduction not exist
     */
    public function getLogsAction($id)
    {
        $dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id);
        if (!$dissertation) {
            throw new NotFoundHttpException(sprintf('Dissertation with identifier of "%s" does not exist', $id));
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        $entity = $em->find('Bundle\DissertationBundle\Entity\Dissertation', $dissertation->getId());
        $logs = $repo->getLogEntries($entity);
        
        $c = count($logs);
        
        for($i = 1; $i <= $c; $i++) {
            $repo->revert($entity, $i);
            $logsDissertation[$i]['title'] = $entity->getTitle();
            $logsDissertation[$i]['body'] = $entity->getBody(); 
            $logsDissertation[$i]['commitTitle'] = $entity->getCommitTitle(); 
            $logsDissertation[$i]['commitBody'] = $entity->getCommitBody(); 
        }
        
        return $logsDissertation;

    }
    
    /**
     * Fetch a Dissertation or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return DissertationInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($dissertation = $this->container->get('n1c0_dissertation.manager.dissertation')->findDissertationById($id))) {
            throw new NotFoundHttpException(sprintf('The dissertation with the \'%s\' id was not found.',$id));
        }

        return $dissertation;
    }
}
