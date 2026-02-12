<?php
// ============================================================
// adminCreate.php — CREATE: Insert a new course + its chapters
// ============================================================
// Accepts: POST multipart/form-data from the course HTML form.
// On success: redirects to adminList.php with a success flash.
// On error:   redirects back with an error flash.
// ============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/upload_helper.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adminList.php');
    exit;
}

// ── Helper: redirect with a flash message stored in session ──
session_start();
function redirectWithFlash(string $url, string $type, string $msg): never {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
    header('Location: ' . $url);
    exit;
}

// ── 1. Collect & validate course-level fields ─────────────────
$category            = trim($_POST['category']            ?? '');
$title               = trim($_POST['title']               ?? '');
$primary_description = trim($_POST['primary_description'] ?? '');
$learning_outcomes   = trim($_POST['learning_outcomes']   ?? ''); // already joined by JS

$errors = [];

if (empty($category))            $errors[] = 'Category is required.';
if (empty($title))               $errors[] = 'Title is required.';
if (empty($primary_description)) $errors[] = 'Primary description is required.';
if (empty($learning_outcomes))   $errors[] = 'At least one learning outcome is required.';

if (strlen($category)            > 50)   $errors[] = 'Category must be ≤ 50 characters.';
if (strlen($title)               > 100)  $errors[] = 'Title must be ≤ 100 characters.';
if (strlen($primary_description) > 1000) $errors[] = 'Description must be ≤ 1000 characters.';
if (strlen($learning_outcomes)   > 500)  $errors[] = 'Learning outcomes must be ≤ 500 characters.';

// ── 2. Validate chapters ──────────────────────────────────────
$chapterTitles = $_POST['chapter_titles'] ?? [];
$chapterFiles  = $_FILES['chapter_files'] ?? [];

// Re-index the chapter_files array so we can iterate like a normal array
$chaptersCount = count($chapterTitles);

if ($chaptersCount === 0) {
    $errors[] = 'At least one chapter is required.';
} else {
    for ($i = 0; $i < $chaptersCount; $i++) {
        $ct = trim($chapterTitles[$i] ?? '');
        if (empty($ct)) {
            $errors[] = "Chapter " . ($i + 1) . ": title is required.";
        } elseif (strlen($ct) > 100) {
            $errors[] = "Chapter " . ($i + 1) . ": title must be ≤ 100 characters.";
        }

        if (!isset($chapterFiles['error'][$i]) || $chapterFiles['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Chapter " . ($i + 1) . ": a content file is required.";
        }
    }
}

if (!empty($errors)) {
    redirectWithFlash('index.html', 'error', implode(' | ', $errors));
}

// ── 3. Upload course thumbnail (optional) ────────────────────
global $ALLOWED_IMAGE_TYPES;
$imagePath = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    try {
        $imagePath = uploadFile(
            $_FILES['image'],
            UPLOAD_THUMBNAIL_DIR,
            UPLOAD_THUMBNAIL_URL,
            $ALLOWED_IMAGE_TYPES
        );
    } catch (RuntimeException $e) {
        redirectWithFlash('index.html', 'error', 'Thumbnail upload failed: ' . $e->getMessage());
    }
}

// ── 4. Start DB transaction & insert ─────────────────────────
$pdo = getConnection();

try {
    $pdo->beginTransaction();

    // Insert into course table
    $stmt = $pdo->prepare("
        INSERT INTO course (category, title, primary_description, learning_outcomes, image)
        VALUES (:category, :title, :primary_description, :learning_outcomes, :image)
    ");
    $stmt->execute([
        ':category'            => $category,
        ':title'               => $title,
        ':primary_description' => $primary_description,
        ':learning_outcomes'   => $learning_outcomes,
        ':image'               => $imagePath,
    ]);

    $courseId = (int) $pdo->lastInsertId();

    // Insert each chapter into course_content
    $chapterStmt = $pdo->prepare("
        INSERT INTO course_content (course_id, content_title, filepath)
        VALUES (:course_id, :content_title, :filepath)
    ");

    global $ALLOWED_CONTENT_TYPES;

    for ($i = 0; $i < $chaptersCount; $i++) {
        $contentTitle = trim($chapterTitles[$i]);

        // Build a single-file array from the multi-file $_FILES structure
        $singleFile = [
            'name'     => $chapterFiles['name'][$i],
            'type'     => $chapterFiles['type'][$i],
            'tmp_name' => $chapterFiles['tmp_name'][$i],
            'error'    => $chapterFiles['error'][$i],
            'size'     => $chapterFiles['size'][$i],
        ];

        try {
            $filePath = uploadFile(
                $singleFile,
                UPLOAD_CONTENT_DIR,
                UPLOAD_CONTENT_URL,
                $ALLOWED_CONTENT_TYPES
            );
        } catch (RuntimeException $e) {
            $pdo->rollBack();
            // Clean up already-uploaded thumbnail
            deleteFile($imagePath);
            redirectWithFlash('index.html', 'error', 'Chapter ' . ($i + 1) . ' file upload failed: ' . $e->getMessage());
        }

        $chapterStmt->execute([
            ':course_id'     => $courseId,
            ':content_title' => $contentTitle,
            ':filepath'      => $filePath,
        ]);
    }

    $pdo->commit();
    redirectWithFlash('adminList.php', 'success', "Course \"$title\" was created successfully with $chaptersCount chapter(s).");

} catch (PDOException $e) {
    $pdo->rollBack();
    deleteFile($imagePath);
    redirectWithFlash('index.html', 'error', 'Database error: ' . $e->getMessage());
}
?>
