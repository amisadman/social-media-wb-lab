<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'Catgram' ?></title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css" />

    <style>
        /* Catgram Colors (Matching Homepage) */
        :root{
            --cat-bg: #F9FAFB;      /* Light Gray */
            --cat-dark: #1F2937;    /* Dark text */
            --cat-light: #ffffff;   /* White cards */
            --cat-primary: #8B5CF6; /* Violet 500 (Primary accent) */
            --cat-primary-dark: #7C3AED; /* Violet 600 (Hover state) */
            --cat-secondary: #FDE047; /* Yellow 300 (Secondary accent) */
        }
        body {
            background-color: var(--cat-bg);
            color: var(--cat-dark);
            font-family: 'Inter', sans-serif;
        }

        /* General Styling */
        .smooth-card {
            background: var(--cat-light);
            border-radius: 1.5rem; /* Increased rounding */
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .nav-link { color: var(--cat-dark); }
        .nav-link:hover { text-decoration: underline; color: var(--cat-primary); }

        /* Primary Button Style */
        .btn-primary {
            background-color: var(--cat-primary) !important;
            color: var(--cat-light) !important;
            border: none;
            border-radius: 0.75rem; /* Rounded-xl equivalent */
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
            transition: background-color 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            background-color: var(--cat-primary-dark) !important;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.6);
        }

        /* SVG Logo Animation */
        @keyframes ear-twitch {
            0%, 100% { transform: rotate(0deg) }
            50% { transform: rotate(-8deg) }
        }
        /* control svg logo size and animation origin so it scales nicely */
        .logo-svg{ width:48px; height:48px; }
        .logo-ear-left {
            transform-box: fill-box;
            transform-origin: 30% 18%; /* relative origin within the SVG */
            animation: ear-twitch 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Top navigation -->
    <header class="w-full bg-[var(--cat-light)] shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-3">
                <!-- Animated SVG Cat Logo -->
                    <svg class="logo-svg w-12 h-12" width="48" height="48" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Body and Head -->
                    <rect x="50" y="80" width="300" height="150" rx="75" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
                    <!-- Ears -->
                    <g class="logo-ear-left">
                        <path d="M70 80 L130 20 L190 80 H70Z" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
                    </g>
                    <path d="M210 80 L270 20 L330 80 H210Z" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
                    <!-- Inner Ears -->
                    <path d="M90 80 L130 40 L170 80 H90Z" fill="#FDE047"/>
                    <path d="M230 80 L270 40 L310 80 H230Z" fill="#FDE047"/>
                    <!-- Eyes (Winking) -->
                    <circle cx="160" cy="140" r="15" fill="#374151"/>
                    <path d="M260 140 L280 140" stroke="#374151" stroke-width="5" stroke-linecap="round" />
                    <!-- Whiskers -->
                    <path d="M120 170 L80 160" stroke="#374151" stroke-width="2" stroke-linecap="round"/>
                    <path d="M120 180 L80 190" stroke="#374151" stroke-width="2" stroke-linecap="round"/>
                    <path d="M280 170 L320 160" stroke="#374151" stroke-width="2" stroke-linecap="round"/>
                    <path d="M280 180 L320 190" stroke="#374151" stroke-width="2" stroke-linecap="round"/>
                    <!-- Nose and Mouth -->
                    <circle cx="200" cy="175" r="8" fill="#F87171"/>
                    <path d="M200 183 V200 M190 195 Q200 205 210 195" stroke="#F87171" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <!-- Animated Tail -->
                    <g class="cat-tail">
                        <rect x="340" y="120" width="40" height="150" rx="10" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
                    </g>
                    </svg>
                <h1 class="text-2xl font-extrabold text-[#FBBF24] logo">Catgram</h1>
            </a>

            <!-- Search -->
            <div class="flex-1 px-6 hidden md:block relative">
                <input placeholder="Search for cats, tags, or people..."
                       class="w-full p-2.5 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[var(--cat-primary)] focus:border-[var(--cat-primary)] transition duration-150 pr-10" />
                <a href="/search" class="absolute right-8 top-1/2 -translate-y-1/2 text-[var(--cat-primary)] hover:text-[var(--cat-primary-dark)]">
                    <!-- Search Icon (Magnifying Glass) -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </a>
            </div>

            <!-- Nav items -->
            <nav class="flex items-center gap-4 text-sm font-medium">
                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="/dashboard" class="nav-link hidden sm:block">Home</a>
                    <a href="/post/create" class="nav-link hidden sm:block">Create</a>
                    <a href="/logout" class="btn btn-primary h-9 px-4 text-base">Logout</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary h-9 px-4 text-base">Login</a>
                    <!-- Register button uses the outlined style for visual separation, matching the homepage -->
                    <a href="/register" class="h-9 px-4 py-2 text-base font-bold text-[var(--cat-primary)] bg-white border-2 border-[var(--cat-primary)] rounded-xl hover:bg-violet-50 transition duration-300 text-center">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main content -->
    <main class="container mx-auto px-4 mt-8 flex-grow">
          <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="mt-12 text-center text-xs text-gray-600 py-6 border-t border-gray-200">
        <small>&copy; <?= date('Y') ?> Catgram - Made with purrs and passion.</small>
    </footer>

</body>
</html>