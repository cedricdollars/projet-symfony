<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
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
     * @Route("/add-student", name="add-student")
     */
    public function addStudent(Request $request): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            // Valid form and store it to database 
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

        return $this->render("student/studentformPost.html.twig", [
            "form-title" => "Modify student",
            "form_student" => $form->createView()
        ]);
    }
    /**
     * @Route("/delete-student", name="delete_student")
     */
    public function deleteStudent(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $student = $entityManager->getRepository(Student::class)->find($id);
    
        //test if student id exists 
        if(!$student) {
            throw $this->createNotFoundException("There is no student with Id :" .$id);
        }
        $entityManager->remove($student);
        $entityManager->flush();

        return $this->redirectToRoute("students");
    }
}
