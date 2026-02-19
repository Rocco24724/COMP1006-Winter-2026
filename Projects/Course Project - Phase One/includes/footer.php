<!-- Makes the footer of the page go end to end -->
</div>

<!-- Page footer -->
<footer class="bg-dark text-secondary text-center py-3 mt-5">
    <small>🏀 Basketball Team Tracker &mdash; COMP 1006 Course Project </small>
</footer>

<!-- This bootstrap is used for making the delete button work -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Adding the validation to footer and is used if page needs it -->
<?php if (isset($loadValidation) && $loadValidation === true): ?>
    <script src="js/validate.js"></script>
<?php endif; ?>

</body>
</html>
