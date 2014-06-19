<?php

namespace N1c0\DissertationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class LogArgumentController extends FOSRestController
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
     *     404 = "Returned when the entity is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0DissertationBundle:Argument:getLogs.html.twig",
     *  templateVar="logs"   
     * )
     *
     * @param int                   $id                   the entity id
     *
     * @return array
     *
     * @throws NotFoundHttpException when entity not exist
     */
    public function getLogsAction($id, $argumentId)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');

        if ($argument = $this->container->get('n1c0_dissertation.manager.argument')->findArgumentById($argumentId)) {
            $entity = $em->find('Bundle\DissertationBundle\Entity\Argument', $argument->getId());
        }
        else {
            throw new NotFoundHttpException(sprintf('Entity with identifier of "%s" does not exist', $argumentId));
        }

        $logs = $repo->getLogEntries($entity);
        
        $c = count($logs);
        
        for($i = 1; $i <= $c; $i++) {
            $repo->revert($entity, $i);
            $logsEntity[$i]['title'] = $entity->getTitle();
            $logsEntity[$i]['body'] = $entity->getBody(); 
            $logsEntity[$i]['author'] = $entity->getAuthor(); 
            $logsEntity[$i]['date'] = $entity->getCreatedAt()->format('d/m/Y à H:m'); 
            $logsEntity[$i]['commitTitle'] = $entity->getCommitTitle(); 
            $logsEntity[$i]['commitBody'] = $entity->getCommitBody(); 
            $logsEntity[$i]['dissertationId'] = $entity->getDissertation()->getId(); 
        }
        
        return $logsEntity;
    }
}
