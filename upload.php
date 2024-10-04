<?php
// Chemin où les images extraites seront stockées
$extractPath = "extracted_images";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["zip_file"])) {
    // Récupérer les valeurs des champs ajoutés
    $widthImages = isset($_POST['width_images']) ? intval($_POST['width_images']) : 1;
    $pixelsBetween = isset($_POST['pixels_between']) ? intval($_POST['pixels_between']) : 0;

    $zipFile = $_FILES["zip_file"]["tmp_name"]; // Fichier ZIP téléchargé
    $zipFileName = $_FILES["zip_file"]["name"];
    $zipFileType = $_FILES["zip_file"]["type"];
    $fileExtension = strtolower(pathinfo($zipFileName, PATHINFO_EXTENSION));

    // Vérifier si le fichier est un zip par l'extension OU par le type MIME
    if ($fileExtension == "zip" || $zipFileType == "application/zip") {
        // Créer un nouvel objet ZipArchive
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            // Créer le dossier de destination s'il n'existe pas
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0777, true);
            }
            // Extraire le contenu du fichier ZIP
            $zip->extractTo($extractPath);
            $zip->close();

            // Générer le collage (cette fonction est hypothétique et doit être implémentée)
            $collageFileName = generateCollage($extractPath, $widthImages, $pixelsBetween);

            // Télécharger le fichier collage
            if (file_exists($collageFileName)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($collageFileName).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($collageFileName));
                readfile($collageFileName);
                exit;
            }
        } else {
            echo 'Échec de l\'ouverture du fichier ZIP.';
        }
    } else {
        echo "Veuillez télécharger un fichier ZIP valide.";
    }
} else {
    echo "Aucun fichier n'a été téléchargé.";
}

// Fonction pour générer le collage à partir des images extraites
function generateCollage($extractPath, $widthImages, $pixelsBetween) {
    // Dimensions pour chaque image dans le collage (ajuster selon les besoins)
    $widthPerImage = 100;
    $heightPerImage = 100;

    // Obtenez tous les fichiers image du dossier extrait et ses sous-dossiers
    $images = glob($extractPath . '/**/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    // Calculez la taille du collage (ajuster selon les besoins)
    // Pour simplifier, créons un collage carré
    $numImages = count($images);
    $collageSize = ceil($numImages / $widthImages);
    $collageWidth = $widthImages * $widthPerImage + ($widthImages - 1) * $pixelsBetween;
    $collageHeight = $collageSize * $heightPerImage + ($collageSize - 1) * $pixelsBetween;

    // Créez une nouvelle image pour le collage
    $collage = imagecreatetruecolor($collageWidth, $collageHeight);
    $whiteBackground = imagecolorallocate($collage, 255, 255, 255);
    imagefill($collage, 0, 0, $whiteBackground);

    // Placez chaque image dans le collage
    $x = $y = 0;
    foreach ($images as $image) {
        // Pour obtenir le chemin relatif par rapport à $extractPath
        $relativePath = str_replace($extractPath . '/', '', $image);

        $img = @imagecreatefromstring(file_get_contents($image));
        if ($img) {
            // Redimensionnez l'image (cela peut également perdre des proportions)
            $resizedImg = imagescale($img, $widthPerImage, $heightPerImage);
            imagecopy($collage, $resizedImg, $x, $y, 0, 0, $widthPerImage, $heightPerImage);

            // Incrémentez la position x, et ajustez y si nécessaire
            $x += $widthPerImage + $pixelsBetween;
            if ($x >= $collageWidth) {
                $x = 0;
                $y += $heightPerImage + $pixelsBetween;
            }

            // Libérez la mémoire de l'image temporaire
            imagedestroy($img);
            imagedestroy($resizedImg);
        }
    }

    // Sauvegardez le collage
    $collageFileName = $extractPath . '/collage.png';
    imagepng($collage, $collageFileName);

    // Libérez la mémoire
    imagedestroy($collage);

    return $collageFileName;
}
?>
