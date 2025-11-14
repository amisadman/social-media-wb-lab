<?php
$title = 'Create Post | Catgram';
ob_start();
?>

<div class="max-w-2xl mx-auto my-8">
    <div class="smooth-card p-8">
        <h2 class="text-2xl font-bold mb-6 text-[var(--cat-primary)]">Share Your Moment</h2>
        <form method="POST" action="/post/create" class="space-y-4" enctype="multipart/form-data">
            <div>
                <label for="content" class="block text-sm font-medium mb-2 text-[var(--cat-dark)]">What's on your mind?</label>
                <textarea id="content" name="content" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cat-primary)] transition resize-none" placeholder="Share your thoughts..." rows="4"></textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium mb-2 text-[var(--cat-dark)]">Add an image (optional)</label>
                <input type="file" id="image" name="image" accept="image/*" class="w-full p-3 border border-gray-300 rounded-lg cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[var(--cat-primary)] file:text-white file:cursor-pointer hover:file:bg-[var(--cat-primary-dark)] transition" />
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 rounded-lg font-semibold text-white transition-all">Post</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
