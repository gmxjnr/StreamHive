<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container">
    <div class="auth-card">
        <h1>Upload video</h1>

        <form method="POST" enctype="multipart/form-data">
            <input 
                type="text" 
                name="title" 
                placeholder="Titel" 
                required
            >

            <textarea 
                name="description" 
                placeholder="Beschrijving"
                required
            ></textarea>

            <label>Video bestand</label>
            <input 
                type="file" 
                name="video" 
                accept="video/mp4,video/webm,video/ogg" 
                required
            >

            <label>Thumbnail</label>
            <input 
                type="file" 
                name="thumbnail" 
                accept="image/jpeg,image/png,image/webp" 
                required
            >

            <button type="submit">Uploaden</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>