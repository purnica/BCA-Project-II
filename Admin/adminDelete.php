<?php
// ============================================================
// adminDelete.php — DELETE: Remove a course and all its chapters
// ============================================================
// Accepts: POST with `id`
// Deletes: course row, all course_content rows, all uploaded files
// Redirects to adminList.php with a flash message.
// ============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/upload_helper.php';
session_start();

// Only accept POST to prevent accidental GET deletions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adminList.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid course ID.'];
    header('Location: adminList.php');
    exit;
}

$pdo = getConnection();

// Fetch course to confirm existence and get its thumbnail path
$stmt = $pdo->prepare("SELECT id, title, image FROM course WHERE id = :id");
$stmt->execute([':id' => $id]);
$course = $stmt->fetch();

if (!$course) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Course not found.'];
    header('Location: adminList.php');
    exit;
}

// Fetch all chapter file paths before deleting (so we can clean up disk)
$stmt = $pdo->prepare("SELECT filepath FROM course_content WHERE course_id = :cid");
$stmt->execute([':cid' => $id]);
$chapters = $stmt->fetchAll();

try {
    $pdo->beginTransaction();

    // Delete all chapters for this course
    $pdo->prepare("DELETE FROM course_content WHERE course_id = :cid")
        ->execute([':cid' => $id]);

    // Delete the course itself
    $pdo->prepare("DELETE FROM course WHERE id = :id")
        ->execute([':id' => $id]);

    $pdo->commit();

    // ── Remove files from disk (after successful DB deletion) ──

    // Delete chapter content files
    foreach ($chapters as $ch) {
        deleteFile($ch['filepath']);
    }

    // Delete course thumbnail
    if ($course['image']) {
        deleteFile($course['image']);
    }

    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => "Course \"" . htmlspecialchars($course['title']) . "\" and all its chapters have been deleted.",
    ];

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Failed to delete course: ' . $e->getMessage(),
    ];
}

header('Location: adminList.php');
exit;
?>
