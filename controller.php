<?php
	session_start();
	$error="";
	if(isset($_GET['deconnection'])){
		session_destroy();
		header("Location:index.php");
	}
	if(isset($_POST['signup'])){
		if(!(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['login']) && isset($_POST['password']) && isset($_POST['confirm']))){
			$error="les paramètres sont incorrects";
			require 'signup.php';
			exit;
		}else{
			if($_POST['password']!=$_POST['confirm']){
				$error="les mots de passe ne correspondent pas";
				require 'signup.php';
				exit;
			}else{
				$query="INSERT INTO user(nom,prenom,login,password) VALUES(:nom,:prenom,:login,:password)";
				try{
					$bdd = new PDO('mysql:host=localhost;dbname=tchat', 'root', '');
					$req=$bdd->prepare("SELECT * FROM user WHERE login=:login");
					$req->execute(array('login' =>$_POST['login'] ));
					if($req->fetch()){
						$error="ce login existe déjà";
						require 'signup.php';
						exit;
					}else{
						$req=$bdd->prepare($query);
						$req->execute(array(
							'nom'=>htmlspecialchars($_POST['nom']),
							'prenom'=>htmlspecialchars($_POST['prenom']),
							'login'=>htmlspecialchars($_POST['login']),
							'password'=>htmlspecialchars($_POST['password'])
						));
						$_SESSION['login']=$_POST['login'];
						$_SESSION['nom']=$_POST['nom'];
						$_SESSION['prenom']=$_POST['prenom'];
						$_SESSION['password']=$_POST['password'];

						header("Location:account.php");

					}
				}catch(Exception $e){
					die($e->getMessage());
				}
			}
		}
	}else if(isset($_POST['signin'])){
		if(!(isset($_POST['login']) && isset($_POST['password']))){
			$error="les paramètres sont incorrects";
			require 'index.php';
			exit;
		}else{
			try{
				$bdd = new PDO('mysql:host=localhost;dbname=tchat', 'root', '');
				$req=$bdd->prepare("SELECT * FROM user WHERE login=:login AND password=:password");
				$req->execute(array('login' =>htmlspecialchars($_POST['password']),'password'=>htmlspecialchars($_POST['password']) ));
				if($user=$req->fetch()){
					$_SESSION['login']=$user['login'];
					$_SESSION['nom']=$user['nom'];
					$_SESSION['prenom']=$user['prenom'];
					$_SESSION['password']=$user['password'];
					header("Location:account.php");
				}else{
					$error="login ou mot de passe incorrect";
					require 'index.php';
					exit;
				}
			}catch(Exception $e){
				die($e->getMessage());
			}
		}
	}else if(isset($_POST['get_user'])){
		if(!isset($_SESSION['login'])){
			echo json_encode(false);
		}else {
			echo json_encode(
				array(
					'login'=>$_SESSION['login'],
					'nom'=>$_SESSION['nom'],
					'prenom'=>$_SESSION['prenom']
				)
			);
			exit;
		}
	}else if(isset($_POST['add_message'])){
		if(!isset($_SESSION['login'])){
			echo json_encode(false);
		}else {
			try{
				$bdd = new PDO('mysql:host=localhost;dbname=tchat', 'root', '');
				$req=$bdd->prepare("SELECT * FROM user WHERE login=:login AND password=:password");
				$req->execute(array('login' =>htmlspecialchars($_SESSION['password']),'password'=>htmlspecialchars($_SESSION['password']) ));
				if($user=$req->fetch()){
					$req=$bdd->prepare("INSERT INTO message(id_emetteur,contenu,date) VALUES(:id_emetteur,:contenu,:date)");
					$req->execute(array('id_emetteur' =>$user['id_user'],'contenu'=>htmlspecialchars($_POST['message']),'date'=>time()));
					echo json_encode(true);
				}else{
					echo json_encode(false);
				}
			}catch(Exception $e){
				echo json_encode(false);
			}
		}
		exit;
	}else if(isset($_POST['get_message'])){
		if(!isset($_SESSION['login'])){
			echo json_encode(false);
		}else {
			try{
				$bdd = new PDO('mysql:host=localhost;dbname=tchat', 'root', '');
				$req=$bdd->prepare("SELECT * FROM user WHERE login=:login AND password=:password");
				$req->execute(array('login' =>htmlspecialchars($_SESSION['password']),'password'=>htmlspecialchars($_SESSION['password']) ));
				if($user=$req->fetch()){
					$req=$bdd->prepare("SELECT * FROM message INNER JOIN user ON user.id_user=message.id_emetteur");
					$req->execute();
					echo json_encode($req->fetchAll());
				}else{
					echo json_encode(false);
				}
			}catch(Exception $e){
				echo json_encode(false);
			}
		}
	}

	

?>