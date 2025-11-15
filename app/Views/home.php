<?php
$title = 'Catgram';
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catgram - Share Your Feline Life</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --cat-primary: #8B5CF6; /* Violet 500 */
            --cat-secondary: #FDE047; /* Yellow 300 */
            --cat-bg: #F9FAFB; /* Light Gray */
        }
        
        /* Custom animation for the SVG cat */
        @keyframes tail-flick {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(5deg); }
        }
        .cat-tail {
            transform-origin: bottom center;
            animation: tail-flick 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-6xl w-full bg-white shadow-2xl rounded-3xl overflow-hidden md:flex">

        <!-- LEFT SIDE: Content and CTAs -->
        <div class="md:w-1/2 p-8 lg:p-16 flex flex-col justify-center space-y-8">
            <header class="flex flex-row items-center gap-4">
                <h1 class="text-5xl font-extrabold text-gray-900 leading-tight">
                    <span class="text-[#FBBF24] logo">Catgram:</span> Your Feline Life, Shared.
                </h1>
                <p class="mt-4 text-xl text-gray-500">
                    The purr-fect social network for sharing the cutest cat photos and videos on the internet.
                </p>
            </header>



            <!-- CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="/login" class="flex-1 px-8 py-3 text-lg font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700 transition duration-300 text-center shadow-lg shadow-violet-200">
                    Login Now
                </a>
                <a href="/register" class="flex-1 px-8 py-3 text-lg font-bold text-violet-600 bg-white border-2 border-violet-600 rounded-xl hover:bg-violet-50 transition duration-300 text-center">
                    Create Account
                </a>
            </div>

            <p class="text-sm text-gray-400 text-center mt-6">
                Already signed in? <a href="/dashboard" class="text-violet-600 hover:text-violet-800 font-medium">Browse the Dashboard</a>
            </p>
        </div>

        <!-- RIGHT SIDE: Cat Animation/Visual -->
        <div class="md:w-1/2 bg-yellow-100/50 flex items-center justify-center p-8 rounded-r-3xl relative min-h-[300px] md:min-h-full">
            <!-- Custom Animated Cat SVG -->
            <svg class="w-full max-w-sm h-auto drop-shadow-xl" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Body and Head -->
                <rect x="50" y="80" width="300" height="150" rx="75" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
                <!-- Ears -->
                <path d="M70 80 L130 20 L190 80 H70Z" fill="#FFFFFF" stroke="#FBBF24" stroke-width="6"/>
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
            <div class="absolute bottom-5 right-5 text-gray-500 text-xs">
                The Catgram Mascot Winks!
            </div>
        </div>

    </div>

</body>
</html>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
