<?php
include 'api-google/vendor/autoload.php';
putenv('GOOGLE_APPLICATION_CREDENTIALS=COLOCAR LA DIRECCION DE TU ARCHIVO JSON CON LOS PERMISOS DE GOOGLE CLOUD CONSOLE');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/drive.file']);

try { 
    // Definir nombre y carpeta padre para la nueva carpeta
    $nombre = 'Nombre de la Carpeta';
    $carpetaPadre = '1mEtfpPsXdaGmx7n98J-w5S6bYonb_Vpq'; // Reemplaza esto con el ID de la carpeta padre deseada

    // Crear instancia del servicio de Google Drive
    $service = new Google_Service_Drive($client);

    // Crear la nueva carpeta
    $carpeta = new Google_Service_Drive_DriveFile();
    $carpeta->setName($nombre);
    $carpeta->setParents([$carpetaPadre]);
    $carpeta->setDescription('Directorio creado por PHP API GOOGLE DRIVE');
    $carpeta->setMimeType('application/vnd.google-apps.folder');

    $parametros = [
        'fields' => 'id', 
        'supportsAllDrives' => true,
    ];

    // Subir la carpeta a Google Drive
    $nueva_carpeta = $service->files->create($carpeta, $parametros);           

    // Guardar el archivo de forma temporal en una ubicación con 'tmp_name'
    $file_path = $_FILES['archivos']['tmp_name'];

    // Metadatos del archivo
    $file = new Google_Service_Drive_DriveFile();
    $file->setName($_FILES['archivos']['name']);

    // Accedemos al tipo de extensión del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_path);

    // Asignar la nueva carpeta como padre del archivo
    $file->setParents([$nueva_carpeta->id]);

    $file->setDescription("Archivo cargado desde PHP");
    // Colocamos el tipo de archivo que es
    $file->setMimeType($mime_type);

    // Subir el archivo a Google Drive
    $resultado = $service->files->create(
        $file,
        array(
            'data' => file_get_contents($file_path),
            'mimeType' => $mime_type,
            'uploadType' =>'media'
        )
    );

    echo '<a href="https://drive.google.com/open?id=' . $resultado->id . '" target="_blank">'.$resultado->name.'</a>';
} catch (Google_Service_Exception $gs) {
    $mensaje = json_decode($gs->getMessage());
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
