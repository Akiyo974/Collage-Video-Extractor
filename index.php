<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload ZIP pour Collage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="upload-container">
        <h2>Créez ton collage</h2>
        <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
     <input type="file" id="zipFile" name="zip_file" accept=".zip" required>
            <label for="widthImages">Nombre d'images en largeur:</label>
            <input type="number" id="widthImages" name="width_images" min="1" required>
            <label for="pixelsBetween">Nombre de pixels entre les images:</label>
            <input type="number" id="pixelsBetween" name="pixels_between" min="0" required>
            <button type="submit">Upload et Créez le Collage</button>
        </form>
        <div id="message"></div>
    </div>
    <script src="script.js"></script>
</body>
</html>
