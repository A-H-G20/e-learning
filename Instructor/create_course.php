<?php
// Database connection
$host = 'localhost';
$dbname = 'e-learning';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Only proceed if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get course info
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $classification = $_POST['classification'] ?? 'normal';
    $status = $_POST['status'] ?? 'draft';
    $scheduled_publish_time = $_POST['scheduled_publish_time'] ?? null;

    // Accessibility settings
    $include_sign_language = isset($_POST['include_sign_language']) ? 1 : 0;
    $add_captions = isset($_POST['add_captions']) ? 1 : 0;
    $audio_descriptions = isset($_POST['audio_descriptions']) ? 1 : 0;
    $screen_reader = isset($_POST['screen_reader']) ? 1 : 0;
    $keyboard_navigation = isset($_POST['keyboard_navigation']) ? 1 : 0;
    $high_contrast = isset($_POST['high_contrast']) ? 1 : 0;

    // Handle file uploads
    $uploaded_files = [];
    if (!empty($_FILES['course_files']['name'][0])) {
        foreach ($_FILES['course_files']['tmp_name'] as $index => $tmpName) {
            $originalName = $_FILES['course_files']['name'][$index];
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['mp4', 'avi', 'mov', 'mp3', 'wav', 'pdf', 'docx', 'pptx'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = uniqid('file_', true) . '.' . $fileExtension;
                $destination = __DIR__ . '/uploads/' . $newFileName;

                if (!is_dir(__DIR__ . '/uploads')) {
                    mkdir(__DIR__ . '/uploads', 0777, true);
                }

                if (move_uploaded_file($tmpName, $destination)) {
                    $uploaded_files[] = $newFileName;
                }
            }
        }
    }

    $filesJson = !empty($uploaded_files) ? json_encode($uploaded_files) : null;

    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO courses
        (title, description, category, classification, status, scheduled_publish_time,
         include_sign_language, add_captions, audio_descriptions, screen_reader,
         keyboard_navigation, high_contrast, uploaded_files, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $title,
        $description,
        $category,
        $classification,
        $status,
        $scheduled_publish_time,
        $include_sign_language,
        $add_captions,
        $audio_descriptions,
        $screen_reader,
        $keyboard_navigation,
        $high_contrast,
        $filesJson
    ]);

    // Redirect back to course list or success page
    header('Location: ../Instructor/course.php');
    exit;
}
?>
