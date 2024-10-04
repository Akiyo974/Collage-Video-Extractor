document.getElementById('uploadForm').onsubmit = function(event) {
    var fileInput = document.getElementById('zipFile');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.zip)$/i;
    
    if (!allowedExtensions.exec(filePath)) {
        alert('Veuillez télécharger un fichier ZIP.');
        fileInput.value = ''; // Réinitialiser le champ de fichier
        event.preventDefault(); // Empêcher la soumission du formulaire
    }
};
