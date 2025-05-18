<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'BD_inscription');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$theme = $_POST['theme'];

// Check if survey has started
$stmt = $conn->prepare("SELECT DateDebut FROM Sondage WHERE NumS = ?");
$stmt->bind_param("i", $theme);
$stmt->execute();
$result = $stmt->get_result();
$survey = $result->fetch_assoc();

if (strtotime($survey['DateDebut']) > time()) {
    echo "Sondage non encore lancé !";
    exit;
}

// Get total participants
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT r.IdParticipant) as total,
           SUM(CASE WHEN p.Genre = 'F' THEN 1 ELSE 0 END) as totalF,
           SUM(CASE WHEN p.Genre = 'M' THEN 1 ELSE 0 END) as totalM
    FROM Reponse r
    JOIN Participant p ON r.IdParticipant = p.IdParticipant
    WHERE r.NumS = ?
");
$stmt->bind_param("i", $theme);
$stmt->execute();
$result = $stmt->get_result();
$totals = $result->fetch_assoc();

if ($totals['total'] == 0) {
    echo "Aucune participation enregistrée à ce moment";
    exit;
}

// Get questions and their responses
$stmt = $conn->prepare("
    SELECT q.NumQ, q.Contenu,
           COUNT(CASE WHEN r.Rep = 'O' THEN 1 END) as oui,
           COUNT(CASE WHEN r.Rep = 'N' THEN 1 END) as non,
           COUNT(CASE WHEN r.Rep = 'S' THEN 1 END) as sans_avis
    FROM Question q
    LEFT JOIN Reponse r ON q.NumQ = r.NumQ AND q.NumS = r.NumS
    WHERE q.NumS = ?
    GROUP BY q.NumQ, q.Contenu
    ORDER BY q.NumQ
");
$stmt->bind_param("i", $theme);
$stmt->execute();
$result = $stmt->get_result();

// Display statistics
echo "<h1>Statistiques du sondage</h1>";
echo "<p>Nombre total des participants au sondage : " . $totals['total'] . "</p>";
echo "<p>Nombre des femmes : " . $totals['totalF'] . "</p>";
echo "<p>Nombre des hommes : " . $totals['totalM'] . "</p>";

echo "<table border='1'>";
echo "<tr><th>N°</th><th>Question</th><th>Oui</th><th>Non</th><th>Sans avis</th></tr>";

while ($row = $result->fetch_assoc()) {
    $oui_percent = round(($row['oui'] / $totals['total']) * 100, 1);
    $non_percent = round(($row['non'] / $totals['total']) * 100, 1);
    $sans_avis_percent = round(($row['sans_avis'] / $totals['total']) * 100, 1);
    
    echo "<tr>";
    echo "<td>" . $row['NumQ'] . "</td>";
    echo "<td>" . $row['Contenu'] . "</td>";
    echo "<td>" . $oui_percent . "%</td>";
    echo "<td>" . $non_percent . "%</td>";
    echo "<td>" . $sans_avis_percent . "%</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();
?> 