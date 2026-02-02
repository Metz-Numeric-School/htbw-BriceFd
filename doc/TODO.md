# TODO

Suite à un audit effectué en amont, voici les failles et les bugs qui ont été identifés comme prioritaire.

## FAILLES

* Des utilsateurs non admin ont des accès à l'interface de gestion des utilisateurs
J'ai rajouté "guard": "App\\Guard\\AdminGuard" dans config/routes.json

* Les mots de passes ne sont pas chiffrée en base de données...
J'ai rajouté cette ligne pour hasher les mdp quand ils sont enregistrés dans la base de données

* Des injections de type XSS ont été détéctées sur certains formulaires
J'ai changé la fonction insert dans src/repository/habitrepository pour que ça soit une requête préparée
public function insert(array $data = array())
    {
        $pdo = $this->getConnection();
        
        $name = $data['name'];   
        $description = $data['description'];

        // Requête préparée
         $stmt = $pdo->prepare(
            "INSERT INTO habits (user_id, name, description, created_at) VALUES (:user_id, :name, :description, NOW())"
        );

        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, \PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId();
    }

* On nous a signalé des injections SQL lors de la création d'une nouvelles habitudes
  * exemple dans le champs "name" : foo', 'INJECTED-DESC', NOW()); --
   ``$sql = "INSERT INTO habits (user_id, name, description, created_at) VALUES (:user_id, :name, :description, NOW())";
        $query = $this->getConnection()->prepare($sql);
        $query->bindParam(':user_id', $data['user_id']);
        $query->bindParam(':name', $name);
        $query->bindParam(':description', $description);
        $query->execute();
        return $this->getConnection()->lastInsertId();``

## BUGS

* Une 404 est détéctée lors de la redirection après l'ajout d'une habitude
A la création d'une nouvelle habitude il faut changer la redirection en /habits dans src/controller/member/habitscontroller

* Le formulaire d'inscription ne semble pas fonctionner
Il faut remplacer if(!empty($_GET['user'])) par $_POST dans src/controller/registercontroller

* Fatal error: Uncaught Error: Class "App\Controller\Api\HabitsController" lorsque l'on accède à l'URL  ``/api/habits``
j'ai rajouté un "s" à ``class HabitsController extends AbstractController`` dans HabitsController.php

**ATTENTION : certains bugs n'ont pas été listé**

Changer firstname en username dans templates/member/dashboard/index.html.php
