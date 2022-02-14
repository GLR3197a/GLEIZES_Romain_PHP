<?php

    session_start();

    //on verifie que il y a une personne de connecter sinon on renvoie à la page de connexion
    if($_SESSION["autoriser"]!="oui"){
        header("location:connexion.php");
    }

    //on créer une connexion a la base de donnée
   $link = mysqli_connect('localhost', 'root', '', 'test');
   if (!$link) {
      die('Erreur de connexion');
   }

   $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre"));
   
   //on cherche l'id le plus grand
   $tabid = array();
   for($i=0; $i<count($result); $i++){
       array_push($tabid,$result[$i][0]);
   }
   rsort($tabid);

   //on récupére dans des variables les envoient post
    @$chaine = $tabid[0] + 1;
    @$valider = $_POST["valider"];
    @$texte = $_POST["texte"];
    @$deco = $_POST["deconnexion"];
    @$nom = $_SESSION["pseudo"];
    //on créer une variable chaîne de caractère vide pour écrire les messages d'erreur dedans
    $erreur="";

    //On attend que l'utilisateur valide à l'aide du bouton
    if(isset($valider)){
        //si le texte est vide on indique a l'utilisateur que c'est impossible sinon on ajoute son message 
        //et on renvoie a la page d'affichage et les 100 caractères sont impossible a dépasse grace au HTML
        if ($texte == ""){

            $erreur = "Il n'y a pas de message";

        }else{

            $query="INSERT INTO `livre`(`id`, `pseudo`, `message`, `signale`) 
                    VALUES ('$chaine', '$nom','$texte',0)";
            $result1=mysqli_query($link,$query);
            header("location:Livre_dor.php");

        }
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="CSS.css" media="all" rel="stylesheet" type="text/css" />
    <title>message</title>
</head>
<body>

    <a href="Livre_dor.php">🔙</a>
    <a href="deconnexion.php">📴</a>
    <br><br>
    <!--texte de 100 caractères-->
    <form name="fo" method="post" action="">
        <div class="erreur"><?php echo $erreur ?></div>
        <textarea name="texte" maxlength="100" style=resize:none;min-width:100px;min-height:100px;max-width:200px;max-height:200px;></textarea>
        <br>
        <input type="submit" name="valider" value="✔" />
    </form>
    
</body>
</html>
