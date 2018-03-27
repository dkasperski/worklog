<?php

namespace Kasperski\WorklogBundle\Controller;

use Kasperski\WorklogBundle\Exceptions\WorklogNotFoundException;
use Kasperski\WorklogBundle\Form\WorklogType;
use Kasperski\WorklogBundle\Repository\Read\WorklogDoctrineReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class WorklogController extends Controller
{
    /**
     * @Route("/worklog", name="kasperski_worklog")
     */
    public function indexAction()
    {
        return $this->render('WorklogBundle:Worklog:index.html.twig');
    }

    /**
     * @Route("/worklog/record/start", name="kasperski_worklog_record_start")
     */
    public function recordStartAction()
    {
        if(!$this->hasWorklogAlreadyStarted()) {
            $worklogCreatorService = $this->get('kasperski.worklog.creator');

            if ($worklogCreatorService->save()) {
                $this->addFlash(
                    'info',
                    $this->get('translator')->trans('kasperski.worklog.creator.success')
                );
            } else {
                array_map(function ($error) {
                    $this->addFlash('error', $error);
                }, $worklogCreatorService->getErrors());
            }
        }

        return $this->render('WorklogBundle:Worklog:recordStart.html.twig');
    }

    /**
     * @Route("/worklog/record/pause", name="kasperski_worklog_record_pause")
     */
    public function recordPauseAction()
    {
        if(!$this->hasWorklogAlreadyPaused()) {
            $worklogPauseService = $this->get('kasperski.worklog.pause');

            if ($worklogPauseService->pause()) {
                $this->addFlash(
                    'info',
                    $this->get('translator')->trans('kasperski.worklog.pause.success')
                );
            } else {
                array_map(function ($error) {
                    $this->addFlash('error', $error);
                }, $worklogPauseService->getErrors());
            }
        }
        
        return $this->render('WorklogBundle:Worklog:recordPause.html.twig');
    }

    /**
     * @Route("/worklog/record/resume", name="kasperski_worklog_record_resume")
     */
    public function recordResumeAction()
    {
        if(!$this->hasWorklogAlreadyResumed()) {
            $worklogResumeService = $this->get('kasperski.worklog.resume');

            if ($worklogResumeService->resume()) {
                $this->addFlash(
                    'info',
                    $this->get('translator')->trans('kasperski.worklog.resume.success')
                );
            } else {
                array_map(function ($error) {
                    $this->addFlash('error', $error);
                }, $worklogResumeService->getErrors());
            }
        }

        return $this->render('WorklogBundle:Worklog:recordResume.html.twig');
    }

    /**
     * @Route("/worklog/record/stop", name="kasperski_worklog_record_stop")
     */
    public function recordStopAction(Request $request)
    {
        if (!$this->hasWorklogAlreadyStarted()) {
            throw new WorklogNotFoundException(
                $this->get('translator')->trans('kasperski.worklog.not.found.exception')
            );
        }

        $worklog = $this->get('kasperski.worklog.repository.read.session')->get();

        $form = $this->createForm(new WorklogType(), $worklog);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $worklogStopService = $this->get('kasperski.worklog.stop');

            if ($worklogStopService->stop()) {
                $this->addFlash(
                    'info',
                    $this->get('translator')->trans('kasperski.worklog.stop.success')
                );
            } else {
                array_map(function ($error) {
                    $this->addFlash('error', $error);
                }, $worklogStopService->getErrors());
            }

            return $this->redirectToRoute('kasperski_worklog');
        }

        return $this->render(
            'WorklogBundle:Worklog:recordStop.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/worklog/history/{page}/{startedAt}/{stoppedAt}",
     *     name="kasperski_worklog_history",
     *     requirements={"page": "\d+"})
     * @Method({"GET"})
     */
    public function historyAction($page = 1, $startedAt = null, $stoppedAt = null)
    {
        $worklogs = $this->get('kasperski.worklog.repository.read.doctrine')
            ->getForCurrentUser($page, $startedAt, $stoppedAt);
        $totalWorklogsReturned = $worklogs->getIterator()->count();
        $totalWorklogs = $worklogs->count();
        $iterator = $worklogs->getIterator();

        $maxPages = ceil($totalWorklogs / WorklogDoctrineReadRepository::WORKLOG_LIMIT);

        return $this->render('WorklogBundle:Worklog:history.html.twig', [
            'worklogs' => $worklogs,
            'totalWorklogsReturned' => $totalWorklogsReturned,
            'totalWorklogs' => $totalWorklogs,
            'iterator' => $iterator,
            'maxPages' => $maxPages,
            'thisPage' => $page,
        ]);
    }

    /**
     * @Route("/worklog/history/{page}/{startedAt}/{stoppedAt}",
     *     name="kasperski_worklog_history_export",
     *     requirements={"page": "\d+"})
     * @Method({"POST"})
     */
    public function exportHistoryAction($page = 1, $startedAt = null, $stoppedAt = null)
    {
        if (!$this->get('request')->isMethod('post')) {
            throw new MethodNotAllowedHttpException(['post']);
        }
        
        $worklogHistoryExportService = $this->get('kasperski.worklog.history.export');
        $file = $worklogHistoryExportService->export($startedAt, $stoppedAt);

        return $file;
    }

    /**
     * @return bool
     */
    private function hasWorklogAlreadyStarted()
    {
        return $this->get('kasperski.worklog.repository.read.session')->has();
    }

    /**
     * @return bool
     * @throws WorklogNotFoundException
     */
    private function hasWorklogAlreadyPaused()
    {
        if (!$this->hasWorklogAlreadyStarted()) {
            throw new WorklogNotFoundException(
                $this->get('translator')->trans('kasperski.worklog.pause.exception')
            );
        }

        $worklog = $this->get('kasperski.worklog.repository.read.session')->get();
        if ($worklog->getPausedAt()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws WorklogNotFoundException
     */
    private function hasWorklogAlreadyResumed()
    {
        if (!$this->hasWorklogAlreadyStarted()) {
            throw new WorklogNotFoundException(
                $this->get('translator')->trans('kasperski.worklog.resume.exception')
            );
        }

        $worklog = $this->get('kasperski.worklog.repository.read.session')->get();
        if ($worklog->getResumedAt()) {
            return true;
        }

        return false;
    }
}
