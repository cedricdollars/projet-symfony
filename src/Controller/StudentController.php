<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentFormType;
use App\Repository\StudentRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }
    /**
     * @Route("/student", name="student")
     */
    public function index()
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }
    /**
     * @Route("/add-student", name="add_student")
     */
    public function addStudent(Request $request): Response
    {
        $data = \json_decode($request->getContent(), \true);
        $firstName = $data['FirstName'];
        $lastName =  $data['LastName'];
        $numEtud = $data['NumEtud'];
        
        $student = new Student();
        $form = $this->createForm(StudentFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            // Valid form and store it to database 
            if(empty($firstName) || empty($lastName) || empty($numEtud)) {
                throw new NotFoundHttpException("Expecting Mandory fields");
            }
            else if(\ctype_digit($numEtud) && \strlen($numEtud) == 10) {
                throw new Exception("The value numEtud must be 10 digits !");
            }
            else {
                $this->studentRepository->saveStudent($firstName, $lastName,$numEtud);
                return new Response("New student saved" .$student->getId());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirect("/students" .$student->getId());
        }

        return $this->render("student/student-formPost.html.twig", [
            "form_title" => " Ajouter un étudiant",
            "form_student" => $form->createView(),
        ]);

    }
    /**
     * @Route("/students", name="students")
     */
    public function students()
    {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();

        return $this->render("student/students.html.twig", [
            "student" => $students
        ]);
    }
    /**
     * @Route("/student/{id}", name="student")
     */
    public function getStudent(int $id): Response
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        
        if(!$student) {
            throw $this->createNotFoundException("There is no student with Id :" .$id);
        }

        return $this->render("student/studentId.html.twig",[
            "student" => $student
        ]);
    }
    /**
     * @Route("/modify-student/{id}", name="modify_student")
     */
    public function setStudent(Request $request, int $id) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $student = $entityManager->getRepository(Student::class)->find($id);
        $form = $this->createForm(StudentFormType::class, $student);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
        }

        return $this->render("student/student-formPost.html.twig", [
            "form_title" => "Modify student",
            "form_student" => $form->createView()
        ]);
    }
    /**
     * @Route("/delete-student", name="delete_student")
     */
    public function deleteStudent(int $id): Response
    {
        
        $student = $this->studentRepository->findOneBy(['id' => $id]);
        //test if student id exists 
        if(!$student) {
            throw $this->createNotFoundException("There is no student with Id :" .$id);
        }
        $this->studentRepository->removeStudent($student);

        return $this->redirectToRoute("students");
    }
}
