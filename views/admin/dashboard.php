
<main class="admin-dashboard">
    <h2>Admin Management Control Panel</h2>
    <div class="metrics-grid">
        <div class="card">
            <h3>Total Restaurants</h3>
            <p><?= htmlspecialchars($counts['restaurants']) ?></p> </div>
        <div class="card">
            <h3>Total Food Items</h3>
            <p><?= htmlspecialchars($counts['menu_items']) ?></p>
        </div>
        <div class="card">
            <h3>Total User Reviews</h3>
            <p><?= htmlspecialchars($counts['reviews']) ?></p>
        </div>
        <div class="card">
            <h3>Total Blog Posts</h3>
            <p><?= htmlspecialchars($counts['posts']) ?></p>
        </div>
    </div>
</main>
