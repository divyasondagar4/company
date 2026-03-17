<?php
$adminTitle = 'Manage Books';

require_once __DIR__ . '/../db.php';

// Ensure the books table has the book_pdf column
$conn->query("ALTER TABLE books ADD COLUMN IF NOT EXISTS book_pdf VARCHAR(255) DEFAULT '' AFTER cover_image");

$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['id'] ?? 0);
$msg = $msgType = '';

// Handle POST actions FIRST (before any HTML is output via header.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'add' || $postAction === 'edit') {
        $title = sanitize($conn, $_POST['title'] ?? '');
        $author = sanitize($conn, $_POST['author'] ?? '');
        $description = sanitize($conn, $_POST['description'] ?? '');
        $status = sanitize($conn, $_POST['status'] ?? 'coming_soon');
        $cover_image = '';
        $book_pdf = '';
        
        // Handle image upload
        if (!empty($_FILES['cover_image']['name'])) {
            $uploadDir = __DIR__ . '/../uploads/books/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $newName = 'book_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $newName)) {
                    $cover_image = 'uploads/books/' . $newName;
                }
            }
        }

        // Handle PDF upload
        if (!empty($_FILES['book_pdf']['name'])) {
            $pdfUploadDir = __DIR__ . '/../uploads/books/pdfs/';
            if (!is_dir($pdfUploadDir)) mkdir($pdfUploadDir, 0755, true);
            
            $pdfExt = strtolower(pathinfo($_FILES['book_pdf']['name'], PATHINFO_EXTENSION));
            if ($pdfExt === 'pdf') {
                $newPdfName = 'book_pdf_' . time() . '_' . rand(100,999) . '.pdf';
                if (move_uploaded_file($_FILES['book_pdf']['tmp_name'], $pdfUploadDir . $newPdfName)) {
                    $book_pdf = 'uploads/books/pdfs/' . $newPdfName;
                }
            }
        }
        
        if ($postAction === 'add') {
            $stmt = $conn->prepare("INSERT INTO books (title, author, description, cover_image, book_pdf, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $author, $description, $cover_image, $book_pdf, $status);
            if ($stmt->execute()) {
                $_SESSION['toast_message'] = 'Book added successfully!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'Failed to add book.';
                $_SESSION['toast_type'] = 'error';
            }
        } else {
            $bookId = intval($_POST['id']);
            
            // Build dynamic update query based on what was uploaded
            $query = "UPDATE books SET title=?, author=?, description=?, status=?";
            $types = "ssss";
            $params = [&$title, &$author, &$description, &$status];

            if ($cover_image) {
                // Delete old image
                $old = $conn->query("SELECT cover_image FROM books WHERE id=$bookId")->fetch_assoc();
                if ($old && $old['cover_image'] && file_exists(__DIR__ . '/../' . $old['cover_image'])) {
                    unlink(__DIR__ . '/../' . $old['cover_image']);
                }
                $query .= ", cover_image=?";
                $types .= "s";
                $params[] = &$cover_image;
            }

            if ($book_pdf) {
                // Delete old pdf
                $oldPdf = $conn->query("SELECT book_pdf FROM books WHERE id=$bookId")->fetch_assoc();
                if ($oldPdf && $oldPdf['book_pdf'] && file_exists(__DIR__ . '/../' . $oldPdf['book_pdf'])) {
                    unlink(__DIR__ . '/../' . $oldPdf['book_pdf']);
                }
                $query .= ", book_pdf=?";
                $types .= "s";
                $params[] = &$book_pdf;
            }

            $query .= " WHERE id=?";
            $types .= "i";
            $params[] = &$bookId;

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $_SESSION['toast_message'] = 'Book updated successfully!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'Failed to update book.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header("Location: " . SITE_URL . "/admin/manage_books");
        exit;
    }
    
    if ($postAction === 'delete') {
        $bookId = intval($_POST['id']);
        // Delete image & pdf files
        $old = $conn->query("SELECT cover_image, book_pdf FROM books WHERE id=$bookId")->fetch_assoc();
        if ($old) {
            if ($old['cover_image'] && file_exists(__DIR__ . '/../' . $old['cover_image'])) {
                unlink(__DIR__ . '/../' . $old['cover_image']);
            }
            if ($old['book_pdf'] && file_exists(__DIR__ . '/../' . $old['book_pdf'])) {
                unlink(__DIR__ . '/../' . $old['book_pdf']);
            }
        }
        $conn->query("DELETE FROM books WHERE id=$bookId");
        $_SESSION['toast_message'] = 'Book deleted successfully!';
        $_SESSION['toast_type'] = 'success';
        header("Location: " . SITE_URL . "/admin/manage_books");
        exit;
    }
}

require_once 'header.php';

// Fetch data
$books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
$editBook = null;
if ($action === 'edit' && $editId) {
    $editBook = $conn->query("SELECT * FROM books WHERE id=$editId")->fetch_assoc();
}
?>

<h4 class="mb-4" style="color:var(--sacred-maroon);">
  <i class="fas fa-book me-2" style="color:var(--chandan-gold);"></i>Manage Books
</h4>

<!-- Add / Edit Form -->
<div class="sacred-card mb-4">
  <h5 class="mb-3" style="color:var(--sacred-maroon);">
    <i class="fas <?php echo $editBook ? 'fa-edit' : 'fa-plus-circle'; ?> me-2" style="color:var(--chandan-gold);"></i>
    <?php echo $editBook ? 'Edit Book' : 'Add New Book'; ?>
  </h5>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $editBook ? 'edit' : 'add'; ?>">
    <?php if($editBook): ?>
    <input type="hidden" name="id" value="<?php echo $editBook['id']; ?>">
    <?php endif; ?>
    
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-bold">Title *</label>
        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editBook['title'] ?? ''); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Author</label>
        <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($editBook['author'] ?? ''); ?>">
      </div>
      <div class="col-12">
        <label class="form-label fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editBook['description'] ?? ''); ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Cover Image</label>
        <input type="file" name="cover_image" class="form-control" accept="image/*">
        <?php if(isset($editBook) && $editBook['cover_image']): ?>
        <small class="text-muted">Current: <?php echo basename($editBook['cover_image']); ?></small>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Book PDF File</label>
        <input type="file" name="book_pdf" class="form-control" accept="application/pdf">
        <?php if(isset($editBook) && $editBook['book_pdf']): ?>
        <small class="text-muted">Current: <a href="<?php echo SITE_URL . '/' . $editBook['book_pdf']; ?>" target="_blank" class="text-danger"><i class="fas fa-file-pdf"></i> <?php echo basename($editBook['book_pdf']); ?></a></small>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Status</label>
        <select name="status" class="form-select">
          <option value="active" <?php echo ($editBook['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
          <option value="coming_soon" <?php echo ($editBook['status'] ?? 'coming_soon') === 'coming_soon' ? 'selected' : ''; ?>>Coming Soon</option>
          <option value="inactive" <?php echo ($editBook['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" class="btn-sacred">
          <i class="fas <?php echo $editBook ? 'fa-save' : 'fa-plus'; ?>"></i>
          <?php echo $editBook ? 'Update Book' : 'Add Book'; ?>
        </button>
        <?php if($editBook): ?>
        <a href="<?php echo SITE_URL; ?>/admin/manage_books" class="btn btn-sm btn-outline-secondary ms-2">Cancel</a>
        <?php endif; ?>
      </div>
    </div>
  </form>
</div>

<!-- Books List -->
<div class="sacred-card">
  <h5 class="mb-3" style="color:var(--sacred-maroon);">
    <i class="fas fa-list me-2" style="color:var(--chandan-gold);"></i>All Books
  </h5>
  
  <?php if($books && $books->num_rows > 0): ?>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead style="background:var(--chandan-cream);">
        <tr>
          <th>#</th>
          <th>Cover</th>
          <th>Title</th>
          <th>Author</th>
          <th>PDF</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; while($b = $books->fetch_assoc()): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td>
            <?php if($b['cover_image']): ?>
            <img src="<?php echo SITE_URL . '/' . $b['cover_image']; ?>" alt="Cover" style="width:50px;height:65px;object-fit:cover;border-radius:4px;border:1px solid var(--chandan-light);">
            <?php else: ?>
            <div style="width:50px;height:65px;background:var(--chandan-cream);border-radius:4px;display:flex;align-items:center;justify-content:center;border:1px solid var(--chandan-light);">
              <i class="fas fa-book" style="color:var(--chandan-gold);"></i>
            </div>
            <?php endif; ?>
          </td>
          <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
          <td><?php echo htmlspecialchars($b['author'] ?: 'N/A'); ?></td>
          <td>
              <?php if($b['book_pdf']): ?>
                <a href="<?php echo SITE_URL . '/' . $b['book_pdf']; ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="View PDF">
                    <i class="fas fa-file-pdf"></i> View
                </a>
              <?php else: ?>
                <span class="text-muted small">None</span>
              <?php endif; ?>
          </td>
          <td>
            <?php
            $statusBadge = match($b['status']) {
                'active' => '<span class="badge bg-success">Active</span>',
                'coming_soon' => '<span class="badge" style="background:var(--chandan-gold);color:var(--dark-wood);">Coming Soon</span>',
                'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                default => '<span class="badge bg-secondary">Unknown</span>',
            };
            echo $statusBadge;
            ?>
          </td>
          <td><small><?php echo date('d M Y', strtotime($b['created_at'])); ?></small></td>
          <td>
            <a href="?action=edit&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit">
              <i class="fas fa-edit"></i>
            </a>
            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this book?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="text-center py-4" style="color:var(--text-secondary);">
    <i class="fas fa-book-open fa-3x mb-3" style="color:var(--chandan-light);"></i>
    <p>No books added yet. Use the form above to add your first book.</p>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
