    </main>
    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> ART Food Blog &mdash; Project 03, Web Technologies</p>
    </footer>
    <script src="<?= isset($jsPath) ? $jsPath : '../assets/js/main.js' ?>"></script>
    <?php if(isset($extraScripts) && is_array($extraScripts)){ ?>
        <?php foreach($extraScripts as $script){ ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php } ?>
    <?php } ?>
</body>
</html>
