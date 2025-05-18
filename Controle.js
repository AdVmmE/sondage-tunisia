function resetForm() {
    document.getElementById('surveyForm').reset();
}

function Test() {
    // Get form elements
    const mail = document.getElementById('mail').value;
    const password = document.getElementById('password').value;
    const genre = document.getElementById('genre').value;

    // Validate email format
    const emailRegex = /^[a-zA-Z0-9]{3,}@[a-zA-Z0-9]{3,}\.[a-zA-Z]{2,4}$/;
    if (!emailRegex.test(mail)) {
        alert('Format d\'email invalide. L\'email doit contenir au moins 3 caractères alphanumériques avant et après @, et 2-4 lettres après le point.');
        return false;
    }

    // Validate password
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{6}$/;
    if (!passwordRegex.test(password)) {
        alert('Le mot de passe doit contenir exactement 6 caractères, avec au moins une majuscule, une minuscule et un chiffre.');
        return false;
    }

    // Validate genre selection
    if (!genre) {
        alert('Veuillez sélectionner votre genre.');
        return false;
    }

    // Validate that all questions are answered
    const questions = ['q1', 'q2', 'q3'];
    for (const q of questions) {
        const selected = document.querySelector(`input[name="${q}"]:checked`);
        if (!selected) {
            alert('Veuillez répondre à toutes les questions.');
            return false;
        }
    }

    return true;
} 