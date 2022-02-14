<?php
    session_start();

    //on créer une fonction pour voir si le caractère est dans la liste des caractère demander
    function testcaractere($listecaractere, $caractere){
        for($i=0; $i<count($listecaractere); $i++){
            if($listecaractere[$i]==$caractere){
                return true;
            }
        }
        return false;
    }

    //on créer une fonction ou l'on vérifie les condition de création d'un compte
    function verificationcondition(){
        global $nom, $erreurnom, $prenom, $erreurprenom, $id, $erreurlogin, $result, $pass, $erreurpass, $passverif, $erreurpassverif, $grade, $erreurgrade, $testtoutchampsrempli;
        if($nom == ""){
            $erreurnom = "Entrer votre nom";
            $testtoutchampsrempli = false;
        }
        if($prenom == ""){
            $erreurprenom = "Entrer votre prenom";
            $testtoutchampsrempli = false;
        }
        if($id == ""){
            $erreurlogin = "Entrer votre login";
            $testtoutchampsrempli = false;
        }
        if(count($result)!=0){
            $erreurlogin = "Ce login est déjà utilisé";
            $id = "";
            $testtoutchampsrempli = false;
        }
        if($pass == ""){
            $erreurpass = "Entrer votre mot de passe";
            $testtoutchampsrempli = false;
        }else{
            
            if(count(str_split($pass, 1))<8){
                $erreurpass = "Votre mot de passe doit contenir plus de 8 caractères, au moins une majuscule, une minuscule, au moins un des caractères suivant : '@, (, ), |, .'";
                $pass = "";
                $passverif = "";
            }else{
                $tabminuscule = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
                $tabmajuscule = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
                $tabcaracterespecial = array('@','(',')','|','.');
                $minuscule = 0;
                $majuscule = 0;
                $caracterespecial = 0;
                for($i=0; $i<count(str_split($pass, 1)); $i++){
                    if(testcaractere($tabminuscule,str_split($pass, 1)[$i])){
                        $minuscule++;
                    }
                    if(testcaractere($tabmajuscule,str_split($pass, 1)[$i])){
                        $majuscule++;
                    }
                    if(testcaractere($tabcaracterespecial,str_split($pass, 1)[$i])){
                        $caracterespecial++;
                    }
                }
                if($minuscule==0 || $majuscule==0 || $caracterespecial==0){
                    $erreurpass = "Votre mot de passe doit contenir plus de 8 caractères, au moins une majuscule, une minuscule, au moins un des caractères suivant : '@, (, ), |, .'";
                    $pass = "";
                    $passverif = "";
                }
            }

        }
        if($passverif == ""){
            $erreurpassverif = "Verifier votre mot de passe";
            $testtoutchampsrempli = false;
        }else{
            if($pass != $passverif){
                $erreurpass = "Les deux mots de passe ne sont pas identique";
                $testtoutchampsrempli = false;
                $pass = "";
                $passverif = "";
            }
        }
        if($grade == ""){
            $erreurgrade = "Entrer votre grade";
            $testtoutchampsrempli = false;
        }
        if($grade != 0 && $grade != 1){
            $erreurgrade = "Votre grade est incorect (0,1)";
            $grade = "";
            $testtoutchampsrempli = false;
        }
    }

    //on créer une connexion a la base de donnée
    $link = mysqli_connect('localhost', 'root', '', 'test');
    if (!$link) {
        die('Erreur de connexion');
    }

    $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM user"));
    
    //on créer un id avec l'id le plus grand plus 1
    $chaine = $result[count($result)-1][0] + 1;
    //on récupére dans des variables les envoient post
    @$nom = $_POST["nom"];
    @$prenom = $_POST["prenom"];
    @$id = $_POST["id"];
    @$pass = $_POST["pass"]; 
    @$passverif = $_POST["passverif"]; 
    @$grade = $_POST["grade"];
    @$valider = $_POST["valider"];
    //on créer des variables chaîne de caractère vide pour écrire les messages d'erreur dedans
    $erreurnom = "";
    $erreurprenom = "";
    $erreurlogin = "";
    $erreurpass = "";
    $erreurpassverif = "";
    $erreurgrade = "";
    //on créer une variable boolean que l'on initialise à vrai qui indique si tout les champs sont bien remplit
    $testtoutchampsrempli = true;

    //on test la si on a clicker sur le bouton de validation
    if(isset($valider)){

        $result = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM `user` WHERE pseudo='$id'"));

        
        verificationcondition();

        //si les condition sont respecter on créer le profil sinon les message d'erreur safficheront
        if($testtoutchampsrempli){
            $query="INSERT INTO `user`(`id`, `nom`, `prenom`, `pseudo`, `mdp`, `privilege`) 
            VALUES ('$chaine', '$nom','$prenom','$id','$pass','$grade')";
            $result1=mysqli_query($link,$query);
            if($result1){
                $_SESSION["autoriser"]="oui";
                $_SESSION['id']=$id;
                if($grade == 1){
                    $_SESSION["role"]="admin";
                }else{
                    $_SESSION["role"]="pas_admin";
                }
                $_SESSION["pseudo"] = $id;
                header("location:Livre_dor.php");
            }
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
    <title>Inscription</title>
</head>
<body>
    <form name="fo" method="post" action="">
        <div class="erreur"><?php echo $erreurnom ?></div>
        <input type="text" name="nom" placeholder="nom" value="<?php echo $nom ?>"/><br />
        <div class="erreur"><?php echo $erreurprenom ?></div>
        <input type="text" name="prenom" placeholder="prénom" value="<?php echo $prenom ?>"/><br />
        <div class="erreur"><?php echo $erreurlogin ?></div>
        <input type="text" name="id" placeholder="Pseudo" value="<?php echo $id ?>"/><br />
        <div class="erreur"><?php echo $erreurpass ?></div>
        <input type="password" name="pass" placeholder="Mot de passe" value="<?php echo $pass ?>"/><br />
        <div class="erreur"><?php echo $erreurpassverif ?></div>
        <input type="password" name="passverif" placeholder="Mot de passe" value="<?php echo $passverif ?>"/><br />
        <div class="erreur"><?php echo $erreurgrade ?></div>
        <input type="number" name="grade" placeholder="Grade" value="<?php echo $grade ?>"/><br />
        <input type="submit" name="valider" value="S'authentifier" />
    </form>
    <a href="connexion.php">Connexion</a>
</body>
</html>