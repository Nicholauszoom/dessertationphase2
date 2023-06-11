<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{


    protected static $defaultName = 'app:check-status';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        // parent::__construct();
        $this->entityManager = $entityManager;
    }



    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');



        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
           
        ]); 
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new( MailerInterface $mailer ,Request $request, TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
        



         $email = (new Email())
            ->from('nicholaussomi5@gmail.com')
            ->to($task->getTechn())
            ->subject('HelpDesk support system ,You have assigned a new task')
            ->text('You have assigned new task to conduct for more info visit our official website https:\\localhost:8000
             THANK YOU!
            ');

        $mailer ->send($email);    

            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

      
        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {

 // Check if the entity is expired
 $endDate = $task->getEndAt();
 $currentDate = new \DateTime();
 $interval = $currentDate->diff($endDate);
 $isExpired = $interval->invert === 1;
        return $this->render('task/show.html.twig', [
            'task' => $task,
            'isExpired' => $isExpired,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

// task edit status

public function editTaskStatusAction(Request $request, $id)
{
    // Load the task from the database
    $entityManager = $this->$this->getDoctrine()->getManager();
    $task = $entityManager->getRepository(Task::class)->find($id);

    // Create the form
    $form = $this->createFormBuilder()
        ->setAction($this->generateUrl('edit_task_status', ['id' => $id]))
        ->setMethod('POST')
        ->add('status', ChoiceType::class, [
            'choices' => [
                'New' => 'new',
                'In progress' => 'in_progress',
                'Completed' => 'completed',
            ],
            'expanded' => true,
            'multiple' => false,
        ])
        ->add('submit', SubmitType::class, ['label' => 'Update Status'])
        ->getForm();

    // Handle form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $task->setStatus($data['status']); // Set the new status
        $entityManager->flush(); // Save changes to the database
        return $this->redirectToRoute(''); // Redirect to a different page
    }

    // Render the form in your HTML template
    return $this->render('task/show.html.twig', [
        'form' => $form->createView(),
    ]);
}
//end

// protected function execute(InputInterface $input, OutputInterface $output)
// {
//     $repository = $this->entityManager->getRepository(Task::class);
//     $records = $repository->findAll();

//     foreach ($records as $record) {
//         $now = new \DateTime();
//         if ($now >= $record->getEndAt()) {
//             $record->setStatus('expired');
//             $this->entityManager->persist($record);
//         }
//     }

//     $this->entityManager->flush();

//     $output->writeln('Status update completed.');

//     return Command::SUCCESS;
// }


    

}
