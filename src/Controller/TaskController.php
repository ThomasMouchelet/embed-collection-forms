<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tag;
use App\Entity\Task;
use App\Form\TaskType;
use App\Form\TagType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks/create", name="task_create")
     * @Route("/tasks/{id}/edit/", name="task_edit")
     */
    public function new(Task $task = null, Request $request, EntityManagerInterface $em)
    {
        if (!$task){
            $task = new Task();
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        $tags = $task->getTags();

        foreach ($tags as $k => $v){
            $tag = $tags[$k];
            $task->addTag($tag);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();
        }

        return $this->render('task/form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $task->getId() !== null
        ]);
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function index(TaskRepository $ripo)
    {
        $tasks = $ripo->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

}
