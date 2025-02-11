<?php
// src/Controller/TareaController.php
namespace App\Controller;

use App\Entity\Tarea;
use App\Repository\TareaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TareaController extends AbstractController
{
    private $formFactory;
    private $tareaRepository;
    private $entityManager;

    // Inyectar el servicio form.factory en el constructor
    public function __construct(FormFactoryInterface $formFactory, TareaRepository $tareaRepository, EntityManagerInterface $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->tareaRepository = $tareaRepository;
        $this->entityManager = $entityManager;
    }

    // Mostrar todas las tareas
    #[Route('/', name: 'app_tarea_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $completadas = $request->query->get('completadas', false);

        if ($completadas === 'true') {
            $tareas = $em->getRepository(Tarea::class)->findBy(['completada' => true]);
        } else {
            $tareas = $em->getRepository(Tarea::class)->findAll();
        }

        return $this->render('tarea/index.html.twig', [
            'tareas' => $tareas,
            'completadas' => $completadas,
        ]);
    }

    // Agregar tarea
    #[Route('/add', name: 'app_tarea_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $tarea = new Tarea();

        // Usar el formulario inyectado
        $form = $this->createFormBuilder($tarea)
            ->add('tarea', TextType::class, ['label' => 'Tarea'])
            ->add('prioridad', ChoiceType::class, [
                'label' => 'Prioridad',
                'choices' => [
                    'Muy alta' => 1,
                    'Alta' => 2,
                    'Media' => 3,
                    'Baja' => 4,
                    'Muy baja' => 5,
                ],
                'expanded' => false, // Si lo pones en true, se mostrará como botones de opción (radio buttons)
                'multiple' => false, // Solo se puede seleccionar una opción
            ])
            ->add('save', SubmitType::class, ['label' => 'Agregar Tarea', 'attr' => ['class' => 'btn btn-primary']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($tarea->getTarea() === null || $tarea->getTarea() === '') {
                // Esto previene la persistencia de una tarea vacía
                $this->addFlash('error', 'El campo tarea no puede estar vacío.');
                return $this->redirectToRoute('app_tarea_add');
            }
            $em->persist($tarea);
            $em->flush();

            return $this->redirectToRoute('app_tarea_index');
        }

        return $this->render('tarea/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Editar tarea
    #[Route('/edit/{id}', name: 'app_tarea_edit')]
    public function edit(Tarea $tarea, Request $request, EntityManagerInterface $em): Response
    {
        // Usar el formulario inyectado
        $form = $this->createFormBuilder($tarea)
            ->add('tarea', TextType::class, ['label' => 'Título'])
            ->add('prioridad', ChoiceType::class, [
                'label' => 'Prioridad',
                'choices' => [
                    'Muy alta' => 1,
                    'Alta' => 2,
                    'Media' => 3,
                    'Baja' => 4,
                    'Muy baja' => 5,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Actualizar Tarea', 'attr' => ['class' => 'btn btn-warning']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_tarea_index');
        }

        return $this->render('tarea/edit.html.twig', [
            'form' => $form->createView(),
            'tarea' => $tarea,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_tarea_delete')]
    public function delete(int $id): RedirectResponse
    {
        $tarea = $this->tareaRepository->find($id);

        // Si no se encuentra la tarea, mostrar un error
        if (!$tarea) {
            $this->addFlash('error', 'La tarea no existe.');
            return $this->redirectToRoute('app_tarea_index');
        }

        // Eliminar la tarea de la base de datos
        $this->entityManager->remove($tarea);
        $this->entityManager->flush();

        $this->addFlash('success', 'Tarea eliminada con éxito.');

        return $this->redirectToRoute('app_tarea_index');
    }

    // Marcar tarea como completada
    #[Route('/complete/{id}', name: 'app_tarea_complete')]
    public function complete(Tarea $tarea, EntityManagerInterface $em): Response
    {
        $tarea->setCompletada(true);
        $em->flush();

        return $this->redirectToRoute('app_tarea_index');
    }
}

?>
