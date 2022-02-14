<?php
    session_start();
    
    //on verifie que il y a une personne de connecter sinon on renvoie à la page de connexion
    if($_SESSION["autoriser"]!="oui"){
        header("location:connexion.php");
    }

    //on verifie si la personne connecter est admin si c'est le cas on lui met a disposition 
    //un lien pour aller sur une page pour voir les message signaler
    if($_SESSION["role"] == "admin"){
        echo ("<a href='signale.php'>Message signale</a><br>");
    }

    //on créer une connexion a la base de donnée
    $link = mysqli_connect('localhost', 'root', '', 'test');
    if (!$link) {
        die('Erreur de connexion');
    }

    //on créer une fonction pour afficher les textes que j'uttiliserai plutard dans le code
    function affichage_text($result){

        //on vérifie que le résultat il y ai des message sinon on affiche le fait qu'ilk n'y en ai pas
        if(count($result)<1){
            echo("<h4>Pas de message pour le moment");
       }else{
            //on créer deux tableaux un pour mettre les message dedans et un pour y mettre les id au cas ou un message est 
            //suprimer et donc il y aurait un probleme dans les id 
            $tab = array();
            $tabid = array();
            //on remplit les 2 tableaux
            for($i=0; $i<count($result); $i++){
                array_push($tabid,$result[$i][0]);
                $tab[$result[$i][0]] = $result[$i];
            }
            //on inverse le tableau pour affiche les derniers messages en haut 
            rsort($tabid);
            //on affiche message par message avec un bouton signaler dans un form avec un input cacher avec l'id 
            //pour pouvoir le récupérer quand une personne veux le signaler
            for($i=0; $i<count($result); $i++){
                echo("<form name='fo' method='post' action=''><input type='number' name='id' value='".($i+1)."' hidden/><p>".$tab[$tabid[$i]][2]."</p>".$tab[$tabid[$i]][1]."<br><input type='submit' name='signaler' value='Signaler' /></form>");
                echo("<br>");
            }
      }
    }

    //on récupére dans des variables les envoient post
    @$id = $_POST["id"];
    @$signaler = $_POST["signaler"];
    @$recherche = $_POST["recherche"];
    @$login = $_POST["login"];
    @$retour = $_POST["retour"];
    //on créer une varible boolean mit a faux qui indique si une personne recherche ou pas
    $testrecherche = false;

    //on test la si on a clicker sur le bouton de la recherche avant l'affichage pour mettre le bouton au dessus
   if(isset($recherche)){
        echo("<form name='fo' method='post' action=''><input type='submit' name='retour' value='🔙' /></form>");
   }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="CSS.css" media="all" rel="stylesheet" type="text/css" />
        <title>Livre d'or</title>
    </head>
    <body>
        <br>
        <form action="" name="fo" method="post">
            <input type="text" name="login" placeholder="Login">
            <input type="submit" name="recherche" value="Recherche" />
        </form>
        <a href="message.php">💬</a>
        <a href="deconnexion.php">📴</a>
    </body>
</html>

<?php

   //on test la si on a clicker sur le bouton de la recherche après l'affichage pour y afficher le texte
    if (isset($recherche)){
        $result1 = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre WHERE pseudo = '$login'"));
        $testrecherche = true;
        affichage_text($result1);
    }

    //on test si le bouton de retour est cliker on remet le boolean a false se qui réaffiche tout les message
    if(isset($retour)){
        $testrecherche = false;
    }
   
    //on vérifie si un bouton signaler est clicker et on récupere l'id dans l'imput cacher 
    if(isset($signaler)){  
        $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre"));
        $r = $result[$id-1][3]+1;
        mysqli_query($link, "UPDATE `livre` SET `signale`='$r' WHERE `id`='".$result[$id-1][0]."'");
    } 

    //si on n'est pas en train de chercher  on affiche tous les messages
    if(!$testrecherche) {
        $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre"));
        affichage_text($result);
    }   
    
?>




