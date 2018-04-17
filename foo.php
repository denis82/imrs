<?

//phpinfo();
//$pdo = new PDO('mysql:host=136.243.24.131;port=0;dbname=cabinet', 'cabinet', 'GVLwDBJaFAdf3LSz');

try {
    $conn = new PDO('mysql:host=localhost;dbname=cabinet', 'cabinet', 'GVLwDBJaFAdf3LSz', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
//var_dump($conn);
?>