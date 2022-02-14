<?php
    session_start();
    

    //on verifie que il y a une personne de connecter sinon on renvoie à la page de connexion
    if($_SESSION["autoriser"]!="oui"){
        header("location:connexion.php");
    }

    //on verifie si la personne connecter est admin si c'est le cas on lui met a disposition 
    //un lien pour aller sur une page pour voir les message signaler
    if($_SESSION["role"] != "admin"){
        header("location:Livre_dor.php");
    }

    //on créer une connexion a la base de donnée
    $link = mysqli_connect('localhost', 'root', '', 'test');
    if (!$link) {
        die('Erreur de connexion');
    }

    //on créer une fonction pour afficher les textes que j'uttiliserai plutard dans le code
    function affichage_text($link){
        //on créer une variable boolean que l'on initialise à vrai qui indique qu'il n'y a ou non des message signaler
        $testpresencemessagesignaler = true;

        $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre"));
        
        if(count($result)>0){
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
            //on affiche message par message avec un bouton supprimer dans un form avec un input cacher avec l'id 
            //pour pouvoir le récupérer quand une personne veux le supprimer mais que si il a au moins 1 signalement
            for($i=0; $i<count($result); $i++){
                if($result[$i][3]>0){
                    echo("<form name='fo' method='post' action=''><input type='number' name='id' value='".($i+1)."' hidden/><p>".$tab[$tabid[$i]][2]."</p>".$tab[$tabid[$i]][1]."<br>Signalement: ".$tab[$tabid[$i]][3]."<input type='submit' name='supprimer' value='Supprimer' /></form>");
                    echo("<br>");
                    //on change le boolean pour indique qu'il y a au moins un message signaler
                    $testpresencemessagesignaler = false;
                }
            }
       }
       //on vérifie s'il n'y a pas de message signaler et afficher un message pour l'indiquer
       if($testpresencemessagesignaler){
           echo("<h4>Il n'y a pas de message signialer</h4>");
       }
    }

    //on récupére dans des variables les envoient post
    @$supprime = $_POST["supprimer"];
    @$id = $_POST["id"];
    @$recherche = $_POST["recherche"];
    @$login = $_POST["login"];
    @$retour = $_POST["retour"];
    //on créer une varible boolean mit a faux qui indique si une personne recherche ou pas
    $testrecherche = false;

    //on test la si on a clicker sur le bouton de la recherche avant l'affichage pour mettre le bouton au dessus
   if(isset($recherche)){
        echo("<form name='fo' method='post' action=''><input type='submit' name='retour' value='Retour' /></form>");
   }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="CSS.css" media="all" rel="stylesheet" type="text/css" />
    <title>Signaler</title>
</head>
<body>
    <a href="Livre_dor.php">🔙</a>
    <br>
    <form action="" name="fo" method="post">
        <input type="text" name="login" placeholder="Login">
        <input type="submit" name="recherche" value="Recherche" />
    </form>
    <a href="deconnexion.php">📴</a>
</body>
</html>

<?php
    //on test la si on a clicker sur le bouton de la recherche après l'affichage pour y afficher le texte
    if (isset($recherche)){
        $testrecherche = true;
        affichage_text($link);
    }   

    //on vérifie si un bouton supprimer est clicker et on récupere l'id dans l'imput cacher 
    if(isset($supprime)){
        $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM livre"));
        mysqli_query($link, "DELETE FROM `livre` WHERE id='".$result[$id-1][0]."'");
    }

    //si on n'est pas en train de chercher  on affiche tous les messages
    if(!$testrecherche) {
        affichage_text($link);
    }   
    
?>

