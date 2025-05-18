<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'BD_inscription');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$mail = $_POST['mail'];
$password = $_POST['password'];
$genre = $_POST['genre'];
$current_survey = 1; // Assuming current survey is about social networks (NumS = 1)

// Check if participant exists
$stmt = $conn->prepare("SELECT IdParticipant, Mdp FROM Participant WHERE Mail = ?");
$stmt->bind_param("s", $mail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Participant exists
    $participant = $result->fetch_assoc();
    
    if ($participant['Mdp'] !== $password) {
        echo "Erreur d'authentification";
        exit;
    }
    
    $idParticipant = $participant['IdParticipant'];
    
    // Check if participant has already answered this survey
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Reponse WHERE IdParticipant = ? AND NumS = ?");
    $stmt->bind_param("ii", $idParticipant, $current_survey);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        // Update existing responses
        $stmt = $conn->prepare("DELETE FROM Reponse WHERE IdParticipant = ? AND NumS = ?");
        $stmt->bind_param("ii", $idParticipant, $current_survey);
        $stmt->execute();
        
        // Insert new responses
        for ($i = 1; $i <= 3; $i++) {
            $response = $_POST["q$i"];
            $questionNum = substr($response, 0, 1);
            $answer = substr($response, 1);
            
            $stmt = $conn->prepare("INSERT INTO Reponse (NumQ, NumS, IdParticipant, Rep) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $questionNum, $current_survey, $idParticipant, $answer);
            $stmt->execute();
        }
        
        echo "Mise à jour effectuée avec succès";
    } else {
        // Insert new responses
        for ($i = 1; $i <= 3; $i++) {
            $response = $_POST["q$i"];
            $questionNum = substr($response, 0, 1);
            $answer = substr($response, 1);
            
            $stmt = $conn->prepare("INSERT INTO Reponse (NumQ, NumS, IdParticipant, Rep) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $questionNum, $current_survey, $idParticipant, $answer);
            $stmt->execute();
        }
        
        echo "Participation au sondage effectuée avec succès";
    }
} else {
    // New participant
    $stmt = $conn->prepare("INSERT INTO Participant (Mail, Mdp, Genre) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $mail, $password, $genre);
    $stmt->execute();
    
    $idParticipant = $conn->insert_id;
    
    // Insert responses
    for ($i = 1; $i <= 3; $i++) {
        $response = $_POST["q$i"];
        $questionNum = substr($response, 0, 1);
        $answer = substr($response, 1);
        
        $stmt = $conn->prepare("INSERT INTO Reponse (NumQ, NumS, IdParticipant, Rep) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $questionNum, $current_survey, $idParticipant, $answer);
        $stmt->execute();
    }
    
    echo "Inscription et participation au sondage effectuées avec succès";
}

$conn->close();
?> 