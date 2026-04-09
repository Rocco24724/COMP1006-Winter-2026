<!-- Closes the container div opened in header.php -->
</div>

<!-- Page footer -->
<footer class="bg-dark text-secondary text-center py-3 mt-5">
    <small>🏀 Basketball Team Tracker &mdash; COMP 1006 Course Project</small>
</footer>

<!-- Bootstrap needed for the navbar toggle and delete function -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Load validate.js only on pages that need it -->
<?php if (isset($loadValidation) && $loadValidation === true): ?>
    <script src="js/validate.js"></script>
<?php endif; ?>

</body>
</html>
