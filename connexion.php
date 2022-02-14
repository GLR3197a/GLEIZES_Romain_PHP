<?php

   session_start();

   //on créer une connexion a la base de donnée
   $link = mysqli_connect('localhost', 'root', '', 'test');
   if (!$link) {
      die('Erreur de connexion');
   }

   //Initialisation des variables
   @$id = $_POST["id"];
   @$pass = $_POST["pass"];
   @$valider = $_POST["valider"];
   $erreur="";

   //On attend que l'utilisateur valide à l'aide du bouton
   if(isset($valider)){
      //On fait une requête SQL dans la base de donnée avec le pseudo de l'utilisateur
      $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM `user` WHERE pseudo='$id'"));

      //Si on n'a pas de résultat c'est que le pseudo n'existe pas donc on le spécifie a l'utilisateur
      if (count($result) == 0){
         $erreur="Mauvais login ou mot de passe!";
      
      }else{
         //sinon on test tout les resultat pour voir si il y a un mot de passe qui correspond
         for($i=0; $i<count($result); $i++){

            $bonLogin = $result[$i][3];
            $bonPass = $result[$i][4];

            //on vérifie si c'est le bon mot de passe
            if($id==$bonLogin && $pass==$bonPass){

               $_SESSION["autoriser"]="oui";
               $_SESSION['id']=$result[$i][1];

               //on verrifie si la personne est un admin
               if($result[$i][5] == 1){

                  $_SESSION["role"]="admin";

               }else{

                  $_SESSION["role"]="pas_admin";

               }

               $_SESSION["pseudo"] = $id;
               header("location:Livre_dor.php");

            }else{

               $erreur="Mauvais login ou mot de passe!";

            }
         }
      }
   }
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <link href="CSS.css" media="all" rel="stylesheet" type="text/css" />
      <title>Connexion</title>
   </head>
   <body>
      <h1>connexion</h1>
      <div class="erreur"><?php echo $erreur ?></div>
      <form name="fo" method="post" action="">
         <input type="text" name="id" placeholder="Pseudo" value="<?php echo $id ?>"/><br />
         <input type="password" name="pass" placeholder="Mot de passe" /><br />
         <input type="submit" name="valider" value="S'authentifier" />
      </form>
      <a href="inscription.php">S'inscrire</a>
   </body>
</html>