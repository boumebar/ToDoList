<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    /**
     * @var TaskRepository
     */
    private $repository;


    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function list()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->repository->findAll()]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function edit(Task $task, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('task_edit', $task, 'Seul le propriétaire peut effectuer ce changement');

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTask(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTask(Task $task, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('task_delete', $task);
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
