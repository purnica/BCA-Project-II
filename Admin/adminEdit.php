<?php
// ============================================================
// adminEdit.php — UPDATE: Edit course details + manage chapters
// ============================================================
// GET  ?id=N  → renders the pre-filled edit form
// POST        → processes updates and redirects
// ============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/upload_helper.php';
session_start();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: adminList.php');
    exit;
}

$pdo = getConnection();

// ── Fetch existing course ─────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM course WHERE id = :id");
$stmt->execute([':id' => $id]);
$course = $stmt->fetch();

if (!$course) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Course not found.'];
    header('Location: adminList.php');
    exit;
}

// Fetch existing chapters
$stmt = $pdo->prepare("SELECT * FROM course_content WHERE course_id = :cid ORDER BY content_id ASC");
$stmt->execute([':cid' => $id]);
$existingChapters = $stmt->fetchAll();

$errors = [];

// ── Handle POST (update) ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Sanitize course fields
    $category            = trim($_POST['category']            ?? '');
    $title               = trim($_POST['title']               ?? '');
    $primary_description = trim($_POST['primary_description'] ?? '');
    $learning_outcomes   = trim($_POST['learning_outcomes']   ?? '');

    if (empty($category))            $errors[] = 'Category is required.';
    if (empty($title))               $errors[] = 'Title is required.';
    if (empty($primary_description)) $errors[] = 'Primary description is required.';
    if (empty($learning_outcomes))   $errors[] = 'At least one learning outcome is required.';
    if (strlen($category)            > 50)   $errors[] = 'Category ≤ 50 chars.';
    if (strlen($title)               > 100)  $errors[] = 'Title ≤ 100 chars.';
    if (strlen($primary_description) > 1000) $errors[] = 'Description ≤ 1000 chars.';
    if (strlen($learning_outcomes)   > 500)  $errors[] = 'Learning outcomes ≤ 500 chars.';

    // 2. New thumbnail (optional replacement)
    $imagePath = $course['image']; // keep existing by default

    global $ALLOWED_IMAGE_TYPES;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {
            $newImagePath = uploadFile(
                $_FILES['image'],
                UPLOAD_THUMBNAIL_DIR,
                UPLOAD_THUMBNAIL_URL,
                $ALLOWED_IMAGE_TYPES
            );
            // Delete old thumbnail from disk
            deleteFile($course['image']);
            $imagePath = $newImagePath;
        } catch (RuntimeException $e) {
            $errors[] = 'Thumbnail upload failed: ' . $e->getMessage();
        }
    }

    // 3. Chapters to delete
    $deleteChapterIds = isset($_POST['delete_chapters']) ? array_map('intval', $_POST['delete_chapters']) : [];

    // 4. New chapters to add
    $newTitles = $_POST['new_chapter_titles'] ?? [];
    $newFiles  = $_FILES['new_chapter_files'] ?? [];

    $newCount = count($newTitles);
    global $ALLOWED_CONTENT_TYPES;

    for ($i = 0; $i < $newCount; $i++) {
        $t = trim($newTitles[$i] ?? '');
        if (empty($t)) {
            $errors[] = "New chapter " . ($i + 1) . ": title is required.";
        }
        if (!isset($newFiles['error'][$i]) || $newFiles['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "New chapter " . ($i + 1) . ": file is required.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Update course row
            $pdo->prepare("
                UPDATE course
                SET category=:category, title=:title, primary_description=:primary_description,
                    learning_outcomes=:learning_outcomes, image=:image
                WHERE id=:id
            ")->execute([
                ':category'            => $category,
                ':title'               => $title,
                ':primary_description' => $primary_description,
                ':learning_outcomes'   => $learning_outcomes,
                ':image'               => $imagePath,
                ':id'                  => $id,
            ]);

            // Delete selected chapters (also remove their files)
            if (!empty($deleteChapterIds)) {
                $in = implode(',', $deleteChapterIds);
                $toDelete = $pdo->query("SELECT filepath FROM course_content WHERE content_id IN ($in) AND course_id = $id")->fetchAll();
                foreach ($toDelete as $row) {
                    deleteFile($row['filepath']);
                }
                $pdo->exec("DELETE FROM course_content WHERE content_id IN ($in) AND course_id = $id");
            }

            // Insert new chapters
            $addStmt = $pdo->prepare("
                INSERT INTO course_content (course_id, content_title, filepath)
                VALUES (:course_id, :content_title, :filepath)
            ");

            for ($i = 0; $i < $newCount; $i++) {
                $singleFile = [
                    'name'     => $newFiles['name'][$i],
                    'type'     => $newFiles['type'][$i],
                    'tmp_name' => $newFiles['tmp_name'][$i],
                    'error'    => $newFiles['error'][$i],
                    'size'     => $newFiles['size'][$i],
                ];

                $filePath = uploadFile($singleFile, UPLOAD_CONTENT_DIR, UPLOAD_CONTENT_URL, $ALLOWED_CONTENT_TYPES);

                $addStmt->execute([
                    ':course_id'     => $id,
                    ':content_title' => trim($newTitles[$i]),
                    ':filepath'      => $filePath,
                ]);
            }

            $pdo->commit();
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Course \"$title\" updated successfully."];
            header('Location: adminView.php?id=' . $id);
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Database error: ' . $e->getMessage();
        } catch (RuntimeException $e) {
            $pdo->rollBack();
            $errors[] = $e->getMessage();
        }
    }

    // Re-fetch chapters in case of partial update
    $stmt = $pdo->prepare("SELECT * FROM course_content WHERE course_id = :cid ORDER BY content_id ASC");
    $stmt->execute([':cid' => $id]);
    $existingChapters = $stmt->fetchAll();

    // Preserve submitted POST values to re-populate the form
    $course['category']            = $category;
    $course['title']               = $title;
    $course['primary_description'] = $primary_description;
    $course['learning_outcomes']   = $learning_outcomes;
}

// Split outcomes back into individual lines for the form
$outcomesArray = array_filter(array_map('trim', explode(',', $course['learning_outcomes'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course — <?= htmlspecialchars($course['title']) ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f0f4f8; color: #1a202c; }

        .topbar {
            background: #1a5490; color: white; padding: 0 32px;
            height: 60px; display: flex; align-items: center; justify-content: space-between;
        }
        .topbar h1 { font-size: 20px; font-weight: 600; }
        .btn-back { background: rgba(255,255,255,0.2); color: white; padding: 9px 20px;
                    border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .btn-back:hover { background: rgba(255,255,255,0.35); }

        .content { padding: 32px; max-width: 860px; margin: 0 auto; }

        .error-box {
            background: #fff5f5; border: 1px solid #fed7d7; border-radius: 8px;
            padding: 16px; margin-bottom: 24px; color: #c53030; font-size: 14px;
        }
        .error-box ul { padding-left: 18px; margin-top: 6px; }

        .card { background: white; border-radius: 10px; padding: 28px;
                box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 24px; }
        .card h2 { font-size: 18px; font-weight: 700; color: #1a5490;
                   padding-bottom: 14px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 7px; color: #4a5568; }
        input[type="text"], textarea, select {
            width: 100%; padding: 11px 14px; border: 1px solid #cbd5e0; border-radius: 6px;
            font-size: 14px; font-family: inherit; transition: border-color 0.2s;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #5cb3cc; }
        textarea { resize: vertical; min-height: 100px; }
        .char-count { font-size: 11px; color: #718096; text-align: right; margin-top: 4px; }

        /* Learning outcomes */
        .outcome-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
        .outcome-row input { flex: 1; }
        .btn-remove-outcome {
            background: #fff5f5; color: #c53030; border: 1px solid #fed7d7;
            padding: 8px 12px; border-radius: 5px; cursor: pointer; font-size: 13px; white-space: nowrap;
        }
        .btn-add-outcome {
            background: #f0fff4; color: #276749; border: 1px solid #c6f6d5;
            padding: 9px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600;
            margin-top: 6px;
        }

        /* Existing chapters */
        .chapter-row {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px;
            border: 1px solid #e2e8f0; border-radius: 7px; margin-bottom: 8px; background: #f7fafc;
        }
        .chapter-row.marked-delete { background: #fff5f5; border-color: #fed7d7; opacity: 0.6; }
        .ch-num {
            width: 30px; height: 30px; border-radius: 50%; background: #1a5490; color: white;
            display: flex; align-items: center; justify-content: center; font-size: 13px;
            font-weight: 700; flex-shrink: 0;
        }
        .ch-info { flex: 1; }
        .ch-info strong { font-size: 14px; display: block; }
        .ch-info small { font-size: 12px; color: #718096; }
        .btn-mark-delete {
            background: #fff5f5; color: #c53030; border: 1px solid #fed7d7;
            padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px;
        }
        .btn-unmark-delete {
            background: #f0fff4; color: #276749; border: 1px solid #c6f6d5;
            padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 12px; display: none;
        }

        /* New chapters */
        .new-chapter-row {
            display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px;
            align-items: start; padding: 14px; background: #f0fff4;
            border: 1px solid #c6f6d5; border-radius: 7px; margin-bottom: 8px;
        }
        .btn-remove-new {
            background: #fff5f5; color: #c53030; border: 1px solid #fed7d7;
            padding: 10px 12px; border-radius: 5px; cursor: pointer; font-size: 13px; align-self: center;
        }
        .btn-add-chapter {
            background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8;
            padding: 10px 18px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600;
            margin-top: 6px; width: 100%;
        }

        /* Thumbnail */
        .current-thumb { max-width: 200px; border-radius: 6px; border: 1px solid #e2e8f0; display: block; margin-bottom: 10px; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 14px; background: #1a5490; color: white; border: none;
            border-radius: 7px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s;
        }
        .btn-submit:hover { background: #144073; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>✏️ Edit Course</h1>
    <a href="adminView.php?id=<?= $id ?>" class="btn-back">← Cancel</a>
</div>

<div class="content">

    <?php if (!empty($errors)): ?>
    <div class="error-box">
        <strong>Please fix the following errors:</strong>
        <ul>
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">

        <!-- ── Course Details ──────────────────────────────── -->
        <div class="card">
            <h2>Course Details</h2>

            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">Select a category</option>
                    <?php foreach (['Programming','Web Development','Data Science','Other'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $course['category'] === $opt ? 'selected' : '' ?>>
                            <?= $opt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Course Title *</label>
                <input type="text" name="title" maxlength="100" required value="<?= htmlspecialchars($course['title']) ?>">
                <div class="char-count"><span id="titleCount"><?= strlen($course['title']) ?></span>/100</div>
            </div>

            <div class="form-group">
                <label>Primary Description *</label>
                <textarea name="primary_description" maxlength="1000" required id="descField"><?= htmlspecialchars($course['primary_description']) ?></textarea>
                <div class="char-count"><span id="descCount"><?= strlen($course['primary_description']) ?></span>/1000</div>
            </div>

            <div class="form-group">
                <label>Learning Outcomes *</label>
                <div id="outcomesContainer">
                    <?php foreach ($outcomesArray as $outcome): ?>
                    <div class="outcome-row">
                        <input type="text" class="outcome-input" placeholder="e.g., Basic PHP Syntax"
                               value="<?= htmlspecialchars($outcome) ?>" required>
                        <button type="button" class="btn-remove-outcome" onclick="removeOutcome(this)">Remove</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn-add-outcome" onclick="addOutcome()">+ Add Outcome</button>
                <div class="char-count"><span id="outcomesCount">0</span>/500</div>
            </div>

            <div class="form-group">
                <label>Course Thumbnail</label>
                <?php if ($course['image']): ?>
                    <img src="<?= htmlspecialchars($course['image']) ?>" class="current-thumb" alt="Current thumbnail">
                    <small style="color:#718096;font-size:12px;">Upload a new image below to replace it.</small>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" style="margin-top:8px;">
            </div>
        </div>

        <!-- ── Existing Chapters ───────────────────────────── -->
        <div class="card">
            <h2>Existing Chapters</h2>
            <?php if (empty($existingChapters)): ?>
                <p style="color:#a0aec0;font-size:14px;">No chapters yet.</p>
            <?php else: ?>
                <?php foreach ($existingChapters as $i => $ch): ?>
                <div class="chapter-row" id="ch-row-<?= $ch['content_id'] ?>">
                    <div class="ch-num"><?= $i + 1 ?></div>
                    <div class="ch-info">
                        <strong><?= htmlspecialchars($ch['content_title']) ?></strong>
                        <small><?= htmlspecialchars($ch['filepath']) ?></small>
                    </div>
                    <button type="button" class="btn-mark-delete"
                            onclick="markDelete(<?= $ch['content_id'] ?>)"
                            id="mark-<?= $ch['content_id'] ?>">
                        🗑 Mark for Deletion
                    </button>
                    <button type="button" class="btn-unmark-delete"
                            onclick="unmarkDelete(<?= $ch['content_id'] ?>)"
                            id="unmark-<?= $ch['content_id'] ?>">
                        ↩ Keep
                    </button>
                    <!-- Hidden checkbox; checked = delete on submit -->
                    <input type="checkbox" name="delete_chapters[]"
                           value="<?= $ch['content_id'] ?>"
                           id="del-chk-<?= $ch['content_id'] ?>"
                           style="display:none;">
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ── Add New Chapters ────────────────────────────── -->
        <div class="card">
            <h2>Add New Chapters</h2>
            <div id="newChaptersContainer"></div>
            <button type="button" class="btn-add-chapter" onclick="addNewChapter()">+ Add New Chapter</button>
        </div>

        <button type="submit" class="btn-submit" onclick="prepareOutcomes()">Save Changes</button>
    </form>
</div>

<script>
    // ── Character counters ──────────────────────────────────────
    document.querySelector('[name="title"]').addEventListener('input', function() {
        document.getElementById('titleCount').textContent = this.value.length;
    });
    document.getElementById('descField').addEventListener('input', function() {
        document.getElementById('descCount').textContent = this.value.length;
    });

    function updateOutcomesCount() {
        const total = [...document.querySelectorAll('.outcome-input')]
            .reduce((sum, el) => sum + el.value.length, 0);
        const counter = document.getElementById('outcomesCount');
        counter.textContent = total;
        counter.style.color = total > 500 ? '#c53030' : '#718096';
    }
    document.addEventListener('input', e => { if (e.target.classList.contains('outcome-input')) updateOutcomesCount(); });
    updateOutcomesCount();

    // ── Learning outcomes ───────────────────────────────────────
    function addOutcome() {
        const div = document.createElement('div');
        div.className = 'outcome-row';
        div.innerHTML = `<input type="text" class="outcome-input" placeholder="e.g., Next Outcome" required>
                         <button type="button" class="btn-remove-outcome" onclick="removeOutcome(this)">Remove</button>`;
        document.getElementById('outcomesContainer').appendChild(div);
    }
    function removeOutcome(btn) {
        const container = document.getElementById('outcomesContainer');
        if (container.children.length > 1) {
            btn.parentElement.remove();
            updateOutcomesCount();
        }
    }

    // ── Mark/unmark existing chapters for deletion ──────────────
    function markDelete(id) {
        document.getElementById('del-chk-' + id).checked = true;
        document.getElementById('ch-row-' + id).classList.add('marked-delete');
        document.getElementById('mark-' + id).style.display = 'none';
        document.getElementById('unmark-' + id).style.display = 'inline-block';
    }
    function unmarkDelete(id) {
        document.getElementById('del-chk-' + id).checked = false;
        document.getElementById('ch-row-' + id).classList.remove('marked-delete');
        document.getElementById('mark-' + id).style.display = 'inline-block';
        document.getElementById('unmark-' + id).style.display = 'none';
    }

    // ── Add new chapter rows ────────────────────────────────────
    function addNewChapter() {
        const div = document.createElement('div');
        div.className = 'new-chapter-row';
        div.innerHTML = `
            <div>
                <label>Chapter Title *</label>
                <input type="text" name="new_chapter_titles[]" placeholder="Chapter Title" required>
            </div>
            <div>
                <label>Content File *</label>
                <input type="file" name="new_chapter_files[]" required>
            </div>
            <button type="button" class="btn-remove-new" onclick="this.parentElement.remove()">✕</button>
        `;
        document.getElementById('newChaptersContainer').appendChild(div);
    }

    // ── Before submit: combine outcomes into hidden input ───────
    function prepareOutcomes() {
        const outcomes = [...document.querySelectorAll('.outcome-input')]
            .map(el => el.value.trim()).filter(Boolean).join(', ');
        let hidden = document.querySelector('input[name="learning_outcomes"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'learning_outcomes';
            document.querySelector('form').appendChild(hidden);
        }
        hidden.value = outcomes;
    }
</script>

</body>
</html>
