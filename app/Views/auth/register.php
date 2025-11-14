<?php
$title = 'Register | Catgram';
ob_start();
?>
<div class="max-w-md mx-auto my-12">
    <div class="smooth-card p-8">
        <h2 class="text-2xl font-bold mb-6 text-[var(--cat-primary)]">Join <span class="text-[#FBBF24] logo">Catgram</span></h2>
        <form method="POST" action="/register" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2 text-[var(--cat-dark)]">Full Name</label>
                <input type="text" name="name" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cat-primary)] transition" placeholder="John Doe" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 text-[var(--cat-dark)]">Email</label>
                <input type="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cat-primary)] transition" placeholder="your@email.com" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-2 text-[var(--cat-dark)]">Password</label>
                <input type="password" name="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cat-primary)] transition" placeholder="••••••••" />
            </div>
            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 rounded-lg font-semibold text-white transition-all">Register</button>
            </div>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">Already have an account? <a href="/login" class="font-semibold text-[var(--cat-primary)] hover:text-[var(--cat-primary-dark)]">Login</a></p>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
