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

// Fetch course by ID
if (!isset($_GET['id'])) {
    die('No course ID specified.');
}

$courseId = intval($_GET['id']);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $classification = $_POST['classification'] ?? 'normal';
    $include_sign_language = isset($_POST['include_sign_language']) ? 1 : 0;
    $add_captions = isset($_POST['add_captions']) ? 1 : 0;
    $audio_descriptions = isset($_POST['audio_descriptions']) ? 1 : 0;
    $screen_reader = isset($_POST['screen_reader']) ? 1 : 0;
    $keyboard_navigation = isset($_POST['keyboard_navigation']) ? 1 : 0;
    $high_contrast = isset($_POST['high_contrast']) ? 1 : 0;
    $status = $_POST['status'] ?? 'draft';
    $scheduled_publish_time = $_POST['scheduled_publish_time'] ?? null;

    // Handle file upload
    $uploaded_files = null;
    if (!empty($_FILES['course_files']['name'][0])) {
        $uploaded_files_arr = [];
        foreach ($_FILES['course_files']['tmp_name'] as $index => $tmpName) {
            $fileName = time() . '_' . basename($_FILES['course_files']['name'][$index]);
            $destination = __DIR__ . '/uploads/' . $fileName;

            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0777, true);
            }

            if (move_uploaded_file($tmpName, $destination)) {
                $uploaded_files_arr[] = $fileName;
            }
        }
        $uploaded_files = json_encode($uploaded_files_arr);
    }

    $sql = "
        UPDATE courses SET 
            title = ?, 
            description = ?, 
            category = ?, 
            classification = ?, 
            include_sign_language = ?, 
            add_captions = ?, 
            audio_descriptions = ?, 
            screen_reader = ?, 
            keyboard_navigation = ?, 
            high_contrast = ?, 
            status = ?, 
            scheduled_publish_time = ? 
            " . ($uploaded_files ? ", uploaded_files = ?" : "") . "
        WHERE id = ?
    ";

    $params = [
        $title,
        $description,
        $category,
        $classification,
        $include_sign_language,
        $add_captions,
        $audio_descriptions,
        $screen_reader,
        $keyboard_navigation,
        $high_contrast,
        $status,
        $scheduled_publish_time
    ];
    if ($uploaded_files) {
        $params[] = $uploaded_files;
    }
    $params[] = $courseId;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Location: course.php');
    exit;
}

// Fetch course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die('Course not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="../AdminHtml/Css/admin.css">
    <style>
    body {
        font-family: 'Secular One', sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    .edit-container {
        max-width: 700px;
        margin: 50px auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    label {
        font-weight: bold;
    }
    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    .checkbox-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .checkbox-group label {
        font-weight: normal;
    }
    </style>
</head>
<body>

<div class="edit-container">
    <h2><i class="fas fa-edit"></i> Edit Course</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Course Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
        </div>

        <div class="form-group">
            <label>Course Description</label>
            <textarea name="description" rows="5" required><?= htmlspecialchars($course['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Course Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($course['category']) ?>" required>
        </div>

        <div class="form-group">
            <label>Classification</label>
            <select name="classification" required>
                <option value="normal" <?= $course['classification'] == 'normal' ? 'selected' : '' ?>>Standard</option>
                <option value="deaf" <?= $course['classification'] == 'deaf' ? 'selected' : '' ?>>Deaf/HoH</option>
                <option value="blind" <?= $course['classification'] == 'blind' ? 'selected' : '' ?>>Blind/Low Vision</option>
            </select>
        </div>

        <div class="form-group checkbox-group">
            <label><input type="checkbox" name="include_sign_language" <?= $course['include_sign_language'] ? 'checked' : '' ?>> Sign Language</label>
            <label><input type="checkbox" name="add_captions" <?= $course['add_captions'] ? 'checked' : '' ?>> Captions</label>
            <label><input type="checkbox" name="audio_descriptions" <?= $course['audio_descriptions'] ? 'checked' : '' ?>> Audio Descriptions</label>
            <label><input type="checkbox" name="screen_reader" <?= $course['screen_reader'] ? 'checked' : '' ?>> Screen Reader</label>
            <label><input type="checkbox" name="keyboard_navigation" <?= $course['keyboard_navigation'] ? 'checked' : '' ?>> Keyboard Navigation</label>
            <label><input type="checkbox" name="high_contrast" <?= $course['high_contrast'] ? 'checked' : '' ?>> High Contrast</label>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="draft" <?= $course['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $course['status'] == 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="form-group">
            <label>Scheduled Publish Time</label>
            <input type="datetime-local" name="scheduled_publish_time" value="<?= $course['scheduled_publish_time'] ? date('Y-m-d\TH:i', strtotime($course['scheduled_publish_time'])) : '' ?>">
        </div>

        <div class="form-group">
            <label>Upload New Files (Optional)</label>
            <input type="file" name="course_files[]" multiple>
            <?php if (!empty($course['uploaded_files'])): ?>
                <small>Existing Files:</small>
                <ul>
                    <?php foreach (json_decode($course['uploaded_files']) as $file): ?>
                        <li><a href="/uploads/<?= htmlspecialchars($file) ?>" target="_blank"><?= htmlspecialchars($file) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="form-group" style="text-align: center;">
            <button type="submit" class="btn-primary">Save Changes</button>
        </div>
    </form>

    <div style="text-align: center;">
        <a href="course.php" style="text-decoration: none;">⬅️ Back to Courses</a>
    </div>
</div>

</body>
</html>
