<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit;
}
require_once "db/db.php";

$stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Admin Dashboard</h2>

    <div class="mb-3 text-end">
        <button class="btn btn-success" onclick="openModal('')">Send to New Email</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Destination</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['destination']) ?></td>
                    <td><?= htmlspecialchars($row['departure_date']) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openModal('<?= htmlspecialchars($row['email']) ?>')">Send Document</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="sendModal" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="sendForm" method="POST" action="send_document.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="sendModalLabel">Send Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Email address</label>
                    <input type="email" name="email" id="modalEmail" class="form-control" placeholder="Enter recipient email" required>
                </div>
                <div class="mb-3">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Email subject" required>
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Type your message..." required></textarea>
                </div>
                <div class="mb-3">
                    <label>Attach PDF</label>
                    <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Send Email</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sendModal = new bootstrap.Modal(document.getElementById('sendModal'));

    function openModal(email) {
        document.getElementById('modalEmail').value = email;
        sendModal.show();
    }
</script>
</body>
</html>
