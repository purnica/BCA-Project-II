<?php
// ============================================================
// adminView.php — READ (single): View one course + its chapters
// ============================================================

require_once __DIR__ . '/db.php';
session_start();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: adminList.php');
    exit;
}

$pdo = getConnection();

// Fetch course
$stmt = $pdo->prepare("SELECT * FROM course WHERE id = :id");
$stmt->execute([':id' => $id]);
$course = $stmt->fetch();

if (!$course) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Course not found.'];
    header('Location: adminList.php');
    exit;
}

// Fetch chapters
$stmt = $pdo->prepare("
    SELECT * FROM course_content
    WHERE course_id = :course_id
    ORDER BY content_id ASC
");
$stmt->execute([':course_id' => $id]);
$chapters = $stmt->fetchAll();

// Helper: detect file type from extension
function getFileType(string $path): string {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) return 'image';
    if (in_array($ext, ['mp4','webm','ogg','avi','mov']))  return 'video';
    if ($ext === 'pdf') return 'pdf';
    return 'file';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course — <?= htmlspecialchars($course['title']) ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f0f4f8; color: #1a202c; }

        .topbar {
            background: #1a5490; color: white; padding: 0 32px;
            height: 60px; display: flex; align-items: center; justify-content: space-between;
        }
        .topbar h1 { font-size: 20px; font-weight: 600; }
        .btn { padding: 9px 20px; border-radius: 6px; font-size: 14px; font-weight: 600;
               text-decoration: none; cursor: pointer; border: none; transition: background 0.2s; }
        .btn-back    { background: rgba(255,255,255,0.2); color: white; }
        .btn-back:hover { background: rgba(255,255,255,0.35); }
        .btn-edit    { background: #5cb3cc; color: white; }
        .btn-edit:hover { background: #4a9fb5; }

        .content { padding: 32px; max-width: 900px; margin: 0 auto; }

        .card {
            background: white; border-radius: 10px; padding: 28px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 24px;
        }
        .card-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0;
        }
        .card-header h2 { font-size: 24px; color: #1a5490; }

        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 20px;
            background: #ebf8ff; color: #2b6cb0; font-size: 13px; font-weight: 600;
        }

        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .meta-item label { display: block; font-size: 12px; font-weight: 700;
                           text-transform: uppercase; letter-spacing: 0.6px; color: #718096; margin-bottom: 4px; }
        .meta-item p { font-size: 14px; color: #2d3748; }

        .field-block { margin-bottom: 20px; }
        .field-block label { display: block; font-size: 12px; font-weight: 700;
                             text-transform: uppercase; letter-spacing: 0.6px; color: #718096; margin-bottom: 6px; }
        .field-block p { font-size: 14px; color: #2d3748; line-height: 1.6; }

        .outcomes-list { list-style: none; padding: 0; }
        .outcomes-list li {
            padding: 6px 0; font-size: 14px; color: #2d3748;
            display: flex; align-items: center; gap: 8px;
        }
        .outcomes-list li::before { content: '✓'; color: #48bb78; font-weight: 700; }

        .thumbnail { max-width: 360px; border-radius: 8px; border: 1px solid #e2e8f0; display: block; margin-top: 8px; }

        /* Chapters */
        .section-title { font-size: 18px; font-weight: 700; color: #1a5490; margin-bottom: 16px; }

        .chapter-item {
            display: flex; align-items: center; gap: 16px; padding: 14px 16px;
            border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px;
            background: #f7fafc; transition: background 0.15s;
        }
        .chapter-item:hover { background: #ebf8ff; }
        .ch-number {
            width: 34px; height: 34px; border-radius: 50%; background: #1a5490; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0;
        }
        .ch-info { flex: 1; }
        .ch-title { font-weight: 600; font-size: 15px; margin-bottom: 2px; }
        .ch-path { font-size: 12px; color: #718096; }
        .ch-type-badge {
            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .type-image { background: #c6f6d5; color: #276749; }
        .type-video { background: #fed7d7; color: #742a2a; }
        .type-pdf   { background: #fefcbf; color: #744210; }
        .type-file  { background: #e2e8f0; color: #4a5568; }
        .ch-link { font-size: 13px; color: #2b6cb0; text-decoration: none; }
        .ch-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>📖 Course Detail</h1>
    <div style="display:flex;gap:10px;">
        <a href="adminList.php" class="btn btn-back">← Back to List</a>
        <a href="adminEdit.php?id=<?= $course['id'] ?>" class="btn btn-edit">Edit Course</a>
    </div>
</div>

<div class="content">

    <!-- Course Info Card -->
    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($course['title']) ?></h2>
            <span class="badge"><?= htmlspecialchars($course['category']) ?></span>
        </div>

        <div class="field-block">
            <label>Description</label>
            <p><?= nl2br(htmlspecialchars($course['primary_description'])) ?></p>
        </div>

        <div class="field-block">
            <label>Learning Outcomes</label>
            <ul class="outcomes-list">
            <?php foreach (explode(',', $course['learning_outcomes']) as $outcome): ?>
                <?php if (trim($outcome)): ?>
                    <li><?= htmlspecialchars(trim($outcome)) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($course['image']): ?>
        <div class="field-block">
            <label>Thumbnail</label>
            <img src="<?= htmlspecialchars($course['image']) ?>" class="thumbnail" alt="Course thumbnail">
        </div>
        <?php endif; ?>
    </div>

    <!-- Chapters Card -->
    <div class="card">
        <div class="section-title">
            Chapters &amp; Content
            <span style="font-size:14px;font-weight:400;color:#718096;margin-left:8px;">
                (<?= count($chapters) ?> chapter<?= count($chapters) !== 1 ? 's' : '' ?>)
            </span>
        </div>

        <?php if (empty($chapters)): ?>
            <p style="color:#a0aec0;font-size:14px;">No chapters added yet.</p>
        <?php else: ?>
            <?php foreach ($chapters as $i => $ch):
                $ftype = getFileType($ch['filepath']); ?>
            <div class="chapter-item">
                <div class="ch-number"><?= $i + 1 ?></div>
                <div class="ch-info">
                    <div class="ch-title"><?= htmlspecialchars($ch['content_title']) ?></div>
                    <div class="ch-path"><?= htmlspecialchars($ch['filepath']) ?></div>
                </div>
                <span class="ch-type-badge type-<?= $ftype ?>"><?= $ftype ?></span>
                <a href="<?= htmlspecialchars($ch['filepath']) ?>" target="_blank" class="ch-link">Open ↗</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
