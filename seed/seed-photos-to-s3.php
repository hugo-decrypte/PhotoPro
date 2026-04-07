<?php
/**
 * Script pour copier les photos de test vers S3 avec les s3_key fixes du fichier SQL
 * 
 * Ce script copie directement les fichiers vers S3 en utilisant les chemins définis
 * dans sql/photo/02_photopro_photo.data.sql
 * 
 * Usage: 
 *   docker-compose run --rm app-photo php /var/seed/seed-photos-to-s3.php
 */

declare(strict_types=1);

// Configuration
$s3Url = getenv('SEAWEED_FILER_URL') ?: 'http://seaweed-filer:8888';
$photosBaseDir = __DIR__ . '/photos';

// Mapping fichiers locaux → s3_key (depuis sql/photo/02_photopro_photo.data.sql)
$photoMappings = [
    // Photos eloi (winter)
    'eloi/winter1.jpg' => 'uploads/bb3b51bd-9de0-4320-9168-1a9b1b8c352d.jpg',
    'eloi/winter2.jpg' => 'uploads/65b078c4-f43e-4289-ad40-0d9dff2d904f.jpg',
    'eloi/winter3.jpg' => 'uploads/9ac1363d-5a63-4f8a-837f-4f37763e15a7.jpg',
    'eloi/winter4.jpg' => 'uploads/8e369475-c93f-467d-a429-fb77e2aa069e.jpg',
    
    // Photos hugo (sea)
    'hugo/sea1.jpg' => 'uploads/e9286ace-b18e-4a55-804c-f14d53f36f2e.jpg',
    'hugo/sea3.jpg' => 'uploads/1ad9f6d5-0bfd-40b7-a0ce-d078db5cdc45.jpg',
    
    // Photos tuline (forest)
    'tuline/forest1.jpg' => 'uploads/40d8354a-3e32-4550-90e0-0e046be0ef74.jpg',
    'tuline/forest2.jpg' => 'uploads/b3b5cdfe-ae49-41cb-a79b-93b11663933a.jpg',
    'tuline/forest3.jpg' => 'uploads/654f4937-dd0f-4847-9e59-9b8aa747aab9.jpg',
    
    // Photos vivien (desert)
    'vivien/desert1.jpg' => 'uploads/2ab11251-e3df-464f-89bc-b6e87888b6aa.jpg',
    'vivien/desert2.jpg' => 'uploads/5d75fca9-fb17-4f77-a99f-fbcfe7889cdf.jpg',
];

echo "=================================\n";
echo "  PhotoPro - Seed Photos to S3  \n";
echo "=================================\n\n";
echo "SeaweedFS Filer: $s3Url\n";
echo "Photos Directory: $photosBaseDir\n\n";

if (!is_dir($photosBaseDir)) {
    die("Erreur: Le dossier '$photosBaseDir' n'existe pas.\n");
}

$totalUploaded = 0;
$totalErrors = 0;

foreach ($photoMappings as $localPath => $s3Key) {
    $fullPath = "$photosBaseDir/$localPath";
    
    if (!file_exists($fullPath)) {
        echo "Fichier introuvable: $localPath\n";
        $totalErrors++;
        continue;
    }
    
    echo "Upload: $localPath → $s3Key... ";
    
    try {
        $result = uploadToS3($s3Url, $fullPath, $s3Key);
        
        if ($result['success']) {
            echo "OK\n";
            $totalUploaded++;
        } else {
            echo "ERREUR: {$result['error']}\n";
            $totalErrors++;
        }
        
    } catch (Exception $e) {
        echo "EXCEPTION: {$e->getMessage()}\n";
        $totalErrors++;
    }
}

echo "\n=================================\n";
echo "Photos uploadées: $totalUploaded\n";
echo "Erreurs: $totalErrors\n";
echo "=================================\n";

exit($totalErrors > 0 ? 1 : 0);

// ============================================================================
// FONCTIONS
// ============================================================================

/**
 * Upload un fichier directement vers SeaweedFS Filer
 * 
 * @param string $filerUrl URL du SeaweedFS Filer
 * @param string $filePath Chemin du fichier local
 * @param string $s3Key Clé S3 (chemin dans le bucket)
 * @return array ['success' => bool, 'error' => string|null]
 */
function uploadToS3(string $filerUrl, string $filePath, string $s3Key): array
{
    if (!file_exists($filePath)) {
        return ['success' => false, 'error' => 'Fichier introuvable'];
    }
    
    $mime = mime_content_type($filePath);
    $fileContent = file_get_contents($filePath);
    
    if ($fileContent === false) {
        return ['success' => false, 'error' => 'Impossible de lire le fichier'];
    }
    
    // PUT request vers SeaweedFS Filer
    $url = rtrim($filerUrl, '/') . '/' . ltrim($s3Key, '/');
    
    $context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => "Content-Type: $mime\r\n",
            'content' => $fileContent,
            'timeout' => 30,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    // Vérifier le code de réponse HTTP
    $headers = function_exists('http_get_last_response_headers') 
        ? http_get_last_response_headers() 
        : (isset($http_response_header) ? $http_response_header : null);
    
    if ($headers && count($headers) > 0) {
        $statusLine = $headers[0];
        
        // 200 OK ou 201 Created
        if (preg_match('/HTTP\/\d\.\d\s+(200|201)/', $statusLine)) {
            return ['success' => true, 'error' => null];
        }
        
        return ['success' => false, 'error' => "HTTP error: $statusLine"];
    }
    
    // Si pas de headers, récupérer l'erreur depuis $php_errormsg ou error_get_last()
    $lastError = error_get_last();
    $errorMsg = $lastError ? $lastError['message'] : 'Échec de la connexion à S3';
    
    return ['success' => false, 'error' => $errorMsg];
}