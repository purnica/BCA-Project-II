<?php
// ============================================================
// adminList.php — READ: List all courses with chapter counts
// ============================================================

require_once __DIR__ . '/db.php';
session_start();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pdo = getConnection();

// Fetch every course with its chapter count in one query
$courses = $pdo->query("
    SELECT
        c.id,
        c.category,
        c.title,
        c.image,
        LEFT(c.primary_description, 120) AS short_desc,
        COUNT(cc.content_id) AS chapter_count
    FROM course c
    LEFT JOIN course_content cc ON cc.course_id = c.id
    GROUP BY c.id
    ORDER BY c.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Manager — All Courses</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            min-height: 100vh;
        }

        /* ── Top bar ─────────────────────────────────────────── */
        .topbar {
            background: #1a5490;
            color: white;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h1 { font-size: 20px; font-weight: 600; letter-spacing: 0.3px; }
        .btn-primary {
            background: #5cb3cc;
            color: white;
            border: none;
            padding: 9px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #4a9fb5; }

        /* ── Flash message ───────────────────────────────────── */
        .flash {
            margin: 20px 32px 0;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ── Content wrapper ─────────────────────────────────── */
        .content { padding: 28px 32px; }

        /* ── Stats row ───────────────────────────────────────── */
        .stats { margin-bottom: 24px; font-size: 14px; color: #555; }
        .stats strong { color: #1a5490; font-size: 18px; }

        /* ── Table ───────────────────────────────────────────── */
        .table-wrap {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead { background: #1a5490; color: white; }
        thead th { padding: 14px 16px; text-align: left; font-weight: 600; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #edf2f7; transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f7fafc; }
        td { padding: 14px 16px; vertical-align: middle; }

        .thumb {
            width: 64px;
            height: 44px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .thumb-placeholder {
            width: 64px;
            height: 44px;
            background: #e2e8f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #a0aec0;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #ebf8ff;
            color: #2b6cb0;
        }

        .chapter-count {
            font-weight: 700;
            color: #1a5490;
        }

        .short-desc { color: #555; font-size: 13px; max-width: 260px; }

        /* ── Action buttons ──────────────────────────────────── */
        .actions { display: flex; gap: 8px; }
        .btn-sm {
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s, opacity 0.2s;
        }
        .btn-view   { background: #ebf8ff; color: #2b6cb0; }
        .btn-view:hover { background: #bee3f8; }
        .btn-edit   { background: #fefcbf; color: #744210; }
        .btn-edit:hover { background: #faf089; }
        .btn-delete { background: #fff5f5; color: #c53030; }
        .btn-delete:hover { background: #fed7d7; }

        /* ── Empty state ─────────────────────────────────────── */
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }
        .empty p { margin-top: 8px; font-size: 14px; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>📚 Course Manager</h1>
    <a href="index.html" class="btn-primary">+ Add New Course</a>
</div>

<?php if ($flash): ?>
<div class="flash <?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<div class="content">
    <div class="stats">
        Showing <strong><?= count($courses) ?></strong> course<?= count($courses) !== 1 ? 's' : '' ?>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Chapters</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($courses)): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty">
                            <div style="font-size:40px">📭</div>
                            <p>No courses yet. <a href="index.html">Add your first course →</a></p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= $course['id'] ?></td>
                    <td>
                        <?php if ($course['image']): ?>
                            <img src="<?= htmlspecialchars($course['image']) ?>" class="thumb" alt="thumb">
                        <?php else: ?>
                            <div class="thumb-placeholder">No img</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($course['title']) ?></strong></td>
                    <td><span class="badge"><?= htmlspecialchars($course['category']) ?></span></td>
                    <td><div class="short-desc"><?= htmlspecialchars($course['short_desc']) ?>…</div></td>
                    <td><span class="chapter-count"><?= $course['chapter_count'] ?></span></td>
                    <td>
                        <div class="actions">
                            <a href="adminView.php?id=<?= $course['id'] ?>" class="btn-sm btn-view">View</a>
                            <a href="adminEdit.php?id=<?= $course['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <form method="POST" action="adminDelete.php" onsubmit="return confirm('Delete this course and all its chapters?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $course['id'] ?>">
                                <button type="submit" class="btn-sm btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
