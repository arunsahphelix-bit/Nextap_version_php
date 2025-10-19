<?php
require_once 'config.php';
require_once 'includes/db.php';

$page_title = "NEXtap";

// Get cards sold count from database with proper error handling
$cards_sold = 50000; // Default value

try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT COUNT(*) as cards_sold FROM nfc_cards WHERE status = 'active'");
        if ($stmt) {
            $cards_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cards_data && isset($cards_data['cards_sold'])) {
                $cards_sold = $cards_data['cards_sold'];
            }
        }
    }
} catch(PDOException $e) {
    // Keep default value if database query fails
    $cards_sold = 50000;
}

// Define steps and features arrays
$steps = [
    ["icon"=>"tap_and_play","title"=>"Tap Your Card","desc"=>"Simply tap your NEXtap card on any smartphone to instantly share your digital profile."],
    ["icon"=>"share","title"=>"Share Your Profile","desc"=>"Your profile is a dynamic, customizable landing page for your contact info, links, and more."],
    ["icon"=>"group_add","title"=>"Connect & Grow","desc"=>"Build a powerful network by easily exchanging contact information and staying connected."]
];

$features = [
    ["title"=>"Eco-Friendly","desc"=>"Reduce your carbon footprint by eliminating paper waste.","img"=>"https://lh3.googleusercontent.com/aida-public/AB6AXuDbkfFyowQ-VgQo6HsdHnvABsvnsl2opwApDdIwLGSfAcRTFjwmwZPpd6PM4I0clSZts3j059FCIp89lpIJK-6luQSNpZFIpxr754R_hIEl1Pn7Jg4xcfpOSuJ7L-vvIiZLg54hc4oI6e22YGzrYPxfv37nEFSiX3yhdS1_EdN7fYHT0RFztyG9xAp3MB5B1lmyl32E9zFvk5iFvIEvj3IsONX1qbvQ7AR4hdwA-HwXnbl4YLVs46Fj5VJN7FINVxQWUTPmQnscPAdk"],
    ["title"=>"Cost-Effective","desc"=>"Save money on printing costs with a one-time purchase.","img"=>"https://lh3.googleusercontent.com/aida-public/AB6AXuDa4bq9084izD3YqxfO_TmNFDX6WrTViTvebdYqTd6rlnxViBl2dTMt--TQpqneAZAPqrkz0NvXWX_bIYAFC7npFeMdZPIGm5nKuE2A91DMIVPzV1Jwvp6WYpWZeH0AKWNqxCAtzO190bUOamPp8ikCVYx5M9xllRMxVSNlKuK9XjuJj10UzzBzDsFTYdXGunb69uSETXISnatQGBoXwLyYWKb5eN5QHfSx47YkNW1cNGrMuhpTSQJRS8GdfVz_E5lA5JWDcPRpBlL3"],
    ["title"=>"Data Analytics","desc"=>"Track engagement and gain insights into your networking efforts.","img"=>"https://lh3.googleusercontent.com/aida-public/AB6AXuD1vziFZg1732wpiInm9rVOcCA33wDsyOXi6RziQLZKHQku8GK56v-eOcg0uxlQPhVfjtmdCh5im4Rs78DZ8992pmMOlZunX2nxhEr9ozbR2YFA22UPeCTf4XsRyyAMhG78kwkl4dA2erlmfLggtYV6LR064T6cT4jmbUvnA0Yp3yFqqjGbNVzmnr0denTXZ4kw0H7nODAiWbg6V5M9m7Jy7EA7PgE92WkHiz_O06NdcCYiIf8X7u1g3plrjBdOlTCW1qw9ij4BexNh"]
];
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NEXtap</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#7f13ec",
                    "background-light": "#f7f6f8",
                    "background-dark": "#141118",
                    "card-light": "#ffffff",
                    "card-dark": "#211c27",
                    "border-light": "#e5e7eb",
                    "border-dark": "#473b54",
                    "text-light": "#374151",
                    "text-muted-light": "#6b7280",
                    "text-muted-dark": "#ab9db9"
                },
                fontFamily: {
                    "display": ["Inter", "Noto Sans", "sans-serif"]
                },
                borderRadius: {
                    "DEFAULT": "0.5rem",
                    "lg": "1rem",
                    "xl": "1.5rem",
                    "full": "9999px"
                },
            },
        },
    }
</script>
<style>
html {
    scroll-behavior: smooth;
}

/* Animation classes */
.fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.fade-in-up.visible {
    opacity: 1;
    transform: translateY(0);
}

.fade-in-left {
    opacity: 0;
    transform: translateX(-30px);
    transition: all 0.6s ease-out;
}

.fade-in-left.visible {
    opacity: 1;
    transform: translateX(0);
}

.fade-in-right {
    opacity: 0;
    transform: translateX(30px);
    transition: all 0.6s ease-out;
}

.fade-in-right.visible {
    opacity: 1;
    transform: translateX(0);
}

.scale-in {
    opacity: 0;
    transform: scale(0.9);
    transition: all 0.6s ease-out;
}

.scale-in.visible {
    opacity: 1;
    transform: scale(1);
}

/* Theme transition */
.theme-transition {
    transition: all 0.3s ease-in-out;
}

/* Dropdown menu animations */
#user-menu, #guest-menu {
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s ease-in-out;
}

#user-menu.show, #guest-menu.show {
    opacity: 1;
    transform: translateY(0);
}

.theme-menu-item {
    transition: all 0.2s ease-in-out;
}

/* Stagger delays for grid items */
.stagger-delay-1 { transition-delay: 0.1s; }
.stagger-delay-2 { transition-delay: 0.2s; }
.stagger-delay-3 { transition-delay: 0.3s; }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display theme-transition">
<div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light dark:bg-background-dark theme-transition overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-5">
<div class="layout-content-container flex flex-col max-w-[960px] flex-1">
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-border-light dark:border-b-[#302839] px-4 sm:px-10 py-3 theme-transition">
<div class="flex items-center gap-4 text-text-light dark:text-white">
<div class="size-6 text-primary">
<svg fill="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
<path d="M36.7273 44C33.9891 44 31.6043 39.8386 30.3636 33.69C29.123 39.8386 26.7382 44 24 44C21.2618 44 18.877 39.8386 17.6364 33.69C16.3957 39.8386 14.0109 44 11.2727 44C7.25611 44 4 35.0457 4 24C4 12.9543 7.25611 4 11.2727 4C14.0109 4 16.3957 8.16144 17.6364 14.31C18.877 8.16144 21.2618 4 24 4C26.7382 4 29.123 8.16144 30.3636 14.31C31.6043 8.16144 33.9891 4 36.7273 4C40.7439 4 44 12.9543 44 24C44 35.0457 40.7439 44 36.7273 44Z"></path>
</svg>
</div>
<h2 class="text-text-light dark:text-white text-xl font-bold leading-tight tracking-[-0.015em]">NEXtap</h2>
</div>
<div class="hidden md:flex flex-1 justify-end items-center gap-8">
<div class="flex items-center gap-9">
<a class="text-text-muted-light dark:text-white/80 hover:text-text-light dark:hover:text-white text-sm font-medium leading-normal theme-transition" href="#how-it-works">How it Works</a>
<a class="text-text-muted-light dark:text-white/80 hover:text-text-light dark:hover:text-white text-sm font-medium leading-normal theme-transition" href="#features">Features</a>
<a class="text-text-muted-light dark:text-white/80 hover:text-text-light dark:hover:text-white text-sm font-medium leading-normal theme-transition" href="#cards-sold">Cards Sold</a>
<a class="text-text-muted-light dark:text-white/80 hover:text-text-light dark:hover:text-white text-sm font-medium leading-normal theme-transition" href="#testimonials">Testimonials</a>
</div>
<div class="flex gap-2 items-center">
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="NextTapBuilder/pages/dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
        <span class="truncate">Dashboard</span>
    </a>
    
    <!-- User Menu -->
    <div class="relative">
        <button id="user-menu-button" class="flex items-center justify-center rounded-xl h-10 w-10 bg-gray-200 dark:bg-[#302839] text-text-light dark:text-white hover:bg-gray-300 dark:hover:bg-[#302839]/80 transition-colors">
            <span class="material-symbols-outlined">more_vert</span>
        </button>
        
        <!-- Dropdown Menu -->
        <div id="user-menu" class="absolute right-0 mt-2 w-48 rounded-xl bg-white dark:bg-[#211c27] border border-border-light dark:border-[#473b54] shadow-lg py-2 z-50 hidden theme-transition">
            <a href="NextTapBuilder/pages/dashboard.php" class="theme-menu-item flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg">dashboard</span>
                Dashboard
            </a>
            <a href="NextTapBuilder/pages/edit-profile.php" class="theme-menu-item flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg">account_circle</span>
                Profile
            </a>
            <button id="theme-toggle-menu" class="theme-menu-item flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg theme-icon">dark_mode</span>
                <span class="theme-text">Dark Mode</span>
            </button>
            <div class="border-t border-border-light dark:border-[#473b54] my-1"></div>
            <a href="NextTapBuilder/api/logout.php" class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg">logout</span>
                Logout
            </a>
        </div>
    </div>
    
<?php else: ?>
    <a href="NextTapBuilder/pages/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
        <span class="truncate">Get Started</span>
    </a>
    <a href="NextTapBuilder/pages/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-gray-200 dark:bg-[#302839] text-text-light dark:text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-gray-300 dark:hover:bg-[#302839]/80 transition-colors theme-transition">
        <span class="truncate">Login</span>
    </a>
    
    <!-- Guest Menu -->
    <div class="relative">
        <button id="guest-menu-button" class="flex items-center justify-center rounded-xl h-10 w-10 bg-gray-200 dark:bg-[#302839] text-text-light dark:text-white hover:bg-gray-300 dark:hover:bg-[#302839]/80 transition-colors">
            <span class="material-symbols-outlined">more_vert</span>
        </button>
        
        <!-- Dropdown Menu -->
        <div id="guest-menu" class="absolute right-0 mt-2 w-48 rounded-xl bg-white dark:bg-[#211c27] border border-border-light dark:border-[#473b54] shadow-lg py-2 z-50 hidden theme-transition">
            <button id="theme-toggle-menu" class="theme-menu-item flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg theme-icon">dark_mode</span>
                <span class="theme-text">Dark Mode</span>
            </button>
            <a href="pages/about.php" class="flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg">info</span>
                About
            </a>
            <a href="NextTapBuilder/minee/contact.php" class="flex items-center w-full px-4 py-2 text-sm text-text-light dark:text-white hover:bg-gray-100 dark:hover:bg-[#302839] theme-transition">
                <span class="material-symbols-outlined mr-3 text-lg">contact_support</span>
                Contact
            </a>
        </div>
    </div>
    
<?php endif; ?>
</div>
</div>
</header>
<main class="flex flex-col gap-16 md:gap-24">
<div class="@container mt-10">
<div class="flex flex-col gap-10 px-4 py-10 @[480px]:gap-8 @[864px]:flex-row items-center">
<div class="flex flex-col gap-6 @[480px]:min-w-[400px] @[480px]:gap-8 @[864px]:justify-center text-center @[864px]:text-left fade-in-left">
<div class="flex flex-col gap-4">
<h1 class="text-text-light dark:text-white text-5xl font-black leading-tight tracking-[-0.033em] @[480px]:text-6xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
    Tap. Connect. Grow.
</h1>
<h2 class="text-text-muted-light dark:text-white/80 text-base font-normal leading-normal @[480px]:text-lg @[480px]:font-normal @[480px]:leading-normal max-w-lg mx-auto @[864px]:mx-0">
    NEXtap is the future of networking. Create and share your digital business card with a single tap.
</h2>
</div>
<div class="flex-wrap gap-4 flex justify-center @[864px]:justify-start">
<?php if (isset($_SESSION['user_id'])): ?>
    <a href="NextTapBuilder/pages/dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
        <span class="truncate">Go to Dashboard</span>
    </a>
<?php else: ?>
    <a href="NextTapBuilder/pages/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
        <span class="truncate">Get Started</span>
    </a>
    <a href="NextTapBuilder/pages/shop.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-6 bg-gray-200 dark:bg-[#302839] text-text-light dark:text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-300 dark:hover:bg-[#302839]/80 transition-colors theme-transition">
        <span class="truncate">Buy NFC Card</span>
    </a>
<?php endif; ?>
</div>
</div>
<div class="w-full @[864px]:w-1/2 relative flex justify-center items-center fade-in-right">
<div class="w-full max-w-sm bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl shadow-lg dark:shadow-none" data-alt="NFC Card mockup with a tap animation" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBERyzcknNxqZ-JnMW_ROBZh0bInUR4MnrtVGBihuCbAkB_D3ndEYvWjswt-KATibcUn1vXE5Ty7gifWRJuJhhqxLNw8phg_byxGt3wQat9hfS3Ep_p4kJYl4E2ranPAymlAKQ_BS2P97qWUjlOR7fzLWKmJdZDRX5qhCah4dq2RZd5GlEfuHR-zCmr5UbvDPrLwBSMkmWGDzzhLTeeMz2PPGCtCt6UHWRybUyi6PMdlzse-76mPGFZn1JIQi2Stsm5Ex3TJP4vqef1");'>
<div class="absolute inset-0 flex items-center justify-center">
<div class="w-20 h-20 bg-cyan-400/50 rounded-full animate-ping"></div>
<div class="w-16 h-16 bg-cyan-400 rounded-full absolute"></div>
</div>
</div>
</div>
</div>
</div>

<!-- Website Builder Section -->
<section class="flex flex-col md:flex-row gap-10 px-4 py-10 @container items-center fade-in-up">
    <div class="flex flex-col gap-6 md:w-1/2">
        <div class="flex flex-col gap-4">
            <h1 class="text-text-light dark:text-white tracking-tight text-3xl font-bold leading-tight @[480px]:text-4xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                Build Your Website in Minutes
            </h1>
            <p class="text-text-muted-light dark:text-white/80 text-base font-normal leading-normal">
                Create a stunning, professional website with our intuitive drag-and-drop builder. No coding required.
            </p>
        </div>
        <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] w-fit hover:bg-primary/90 transition-colors">
            <span class="truncate">Learn More</span>
        </button>
    </div>
    <div class="md:w-1/2">
        <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-xl shadow-2xl shadow-primary/20" data-alt="A simplified animation showcasing the drag-and-drop website builder" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDj6TMLl2tYp1lv9hR9ZZ8e_c9eJ5l3pcd409S6CLjKyHcOV-sGNpYIW993NHtVC3LUA1o1MFqog6dHb31F-h5ijkwxus8tv4qdaCQxtlFG1OUU7X-Wp93D69c-oLBlM4PeLBmYaJivaLjwo75Kd532LSUDU9HveRholwPbD2QcjJCuLbfzjjJgLE8ioQhnjM_SecWmH8-ACY-F3NkInVpLkH9k9v0cQq5DVP7qAI1lMX0O3WSbFfVKTEp85YbcncCBFrptY6JKqqhL");'></div>
    </div>
</section>

<!-- How it Works Section -->
<section id="how-it-works" class="flex flex-col gap-6">
    <h2 class="text-text-light dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em] px-4 text-center fade-in-up">How NEXtap Works</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
        <div class="flex flex-1 gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light dark:bg-[#211c27]/50 backdrop-blur-sm p-6 flex-col items-center text-center fade-in-up stagger-delay-1 theme-transition">
            <div class="text-cyan-400 text-4xl">
                <span class="material-symbols-outlined !text-4xl">tap_and_play</span>
            </div>
            <div class="flex flex-col gap-2">
                <h2 class="text-text-light dark:text-white text-lg font-bold leading-tight">Tap Your Card</h2>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm font-normal leading-normal">Simply tap your NEXtap card on any smartphone to instantly share your digital profile.</p>
            </div>
        </div>
        <div class="flex flex-1 gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light dark:bg-[#211c27]/50 backdrop-blur-sm p-6 flex-col items-center text-center fade-in-up stagger-delay-2 theme-transition">
            <div class="text-cyan-400 text-4xl">
                <span class="material-symbols-outlined !text-4xl">share</span>
            </div>
            <div class="flex flex-col gap-2">
                <h2 class="text-text-light dark:text-white text-lg font-bold leading-tight">Share Your Profile</h2>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm font-normal leading-normal">Your profile is a dynamic, customizable landing page for your contact info, links, and more.</p>
            </div>
        </div>
        <div class="flex flex-1 gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light dark:bg-[#211c27]/50 backdrop-blur-sm p-6 flex-col items-center text-center fade-in-up stagger-delay-3 theme-transition">
            <div class="text-cyan-400 text-4xl">
                <span class="material-symbols-outlined !text-4xl">group_add</span>
            </div>
            <div class="flex flex-col gap-2">
                <h2 class="text-text-light dark:text-white text-lg font-bold leading-tight">Connect & Grow</h2>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm font-normal leading-normal">Build a powerful network by easily exchanging contact information and staying connected.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="px-4 py-10 @container items-center">
    <div class="flex flex-col gap-6 md:w-full fade-in-up">
        <div class="flex flex-col gap-4 text-center">
            <h1 class="text-text-light dark:text-white tracking-tight text-3xl font-bold leading-tight @[480px]:text-4xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                Powerful Features
            </h1>
            <p class="text-text-muted-light dark:text-white/80 text-base font-normal leading-normal">
                Discover how NEXtap can transform your networking experience
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="flex flex-col gap-3 p-4 rounded-xl bg-card-light dark:bg-[#211c27] border border-border-light dark:border-[#473b54] shadow-lg scale-in stagger-delay-1 theme-transition">
                <div class="w-full bg-cover bg-center aspect-video rounded-lg" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDbkfFyowQ-VgQo6HsdHnvABsvnsl2opwApDdIwLGSfAcRTFjwmwZPpd6PM4I0clSZts3j059FCIp89lpIJK-6luQSNpZFIpxr754R_hIEl1Pn7Jg4xcfpOSuJ7L-vvIiZLg54hc4oI6e22YGzrYPxfv37nEFSiX3yhdS1_EdN7fYHT0RFztyG9xAp3MB5B1lmyl32E9zFvk5iFvIEvj3IsONX1qbvQ7AR4hdwA-HwXnbl4YLVs46Fj5VJN7FINVxQWUTPmQnscPAdk')"></div>
                <p class="text-text-light dark:text-white text-base font-medium">Eco-Friendly</p>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Reduce your carbon footprint by eliminating paper waste.</p>
            </div>
            <div class="flex flex-col gap-3 p-4 rounded-xl bg-card-light dark:bg-[#211c27] border border-border-light dark:border-[#473b54] shadow-lg scale-in stagger-delay-2 theme-transition">
                <div class="w-full bg-cover bg-center aspect-video rounded-lg" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuDa4bq9084izD3YqxfO_TmNFDX6WrTViTvebdYqTd6rlnxViBl2dTMt--TQpqneAZAPqrkz0NvXWX_bIYAFC7npFeMdZPIGm5nKuE2A91DMIVPzV1Jwvp6WYpWZeH0AKWNqxCAtzO190bUOamPp8ikCVYx5M9xllRMxVSNlKuK9XjuJj10UzzBzDsFTYdXGunb69uSETXISnatQGBoXwLyYWKb5eN5QHfSx47YkNW1cNGrMuhpTSQJRS8GdfVz_E5lA5JWDcPRpBlL3')"></div>
                <p class="text-text-light dark:text-white text-base font-medium">Cost-Effective</p>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Save money on printing costs with a one-time purchase.</p>
            </div>
            <div class="flex flex-col gap-3 p-4 rounded-xl bg-card-light dark:bg-[#211c27] border border-border-light dark:border-[#473b54] shadow-lg scale-in stagger-delay-3 theme-transition">
                <div class="w-full bg-cover bg-center aspect-video rounded-lg" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuD1vziFZg1732wpiInm9rVOcCA33wDsyOXi6RziQLZKHQku8GK56v-eOcg0uxlQPhVfjtmdCh5im4Rs78DZ8992pmMOlZunX2nxhEr9ozbR2YFA22UPeCTf4XsRyyAMhG78kwkl4dA2erlmfLggtYV6LR064T6cT4jmbUvnA0Yp3yFqqjGbNVzmnr0denTXZ4kw0H7nODAiWbg6V5M9m7Jy7EA7PgE92WkHiz_O06NdcCYiIf8X7u1g3plrjBdOlTCW1qw9ij4BexNh')"></div>
                <p class="text-text-light dark:text-white text-base font-medium">Data Analytics</p>
                <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Track engagement and gain insights into your networking efforts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Cards Sold Section -->
<section id="cards-sold" class="flex flex-col gap-8 py-10 scale-in">
    <div class="relative rounded-xl border border-primary/30 bg-card-light/50 dark:bg-[#211c27]/50 backdrop-blur-sm p-8 md:p-12 overflow-hidden theme-transition">
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-cyan-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -top-16 -right-16 w-80 h-80 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="relative flex flex-col items-center justify-center text-center">
            <p class="text-cyan-400 font-semibold text-lg">NFC Cards Activated Worldwide</p>
            <p class="text-text-light dark:text-white text-7xl md:text-8xl font-black tracking-tighter my-2">
                <span id="cards-counter">0</span>+
            </p>
            <p class="text-text-muted-light dark:text-white/80">Join thousands of professionals who upgraded their networking game</p>
            <a href="NextTapBuilder/pages/shop.php" class="mt-8 flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                <span class="truncate">Get Your Card Now</span>
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="flex flex-col gap-8 py-10">
    <h2 class="text-text-light dark:text-white text-3xl font-bold leading-tight tracking-[-0.015em] px-4 text-center fade-in-up">Loved by Creatives</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
        <div class="flex flex-col gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light/50 dark:bg-[#211c27]/50 backdrop-blur-sm p-6 fade-in-up stagger-delay-1 theme-transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6ojHXY3sFjz2UGeJVD_NEA53yPxXdtqA6cVHkWEpeODGc5s7nO1EoZRbX8vwdkYYhUf6uUBIxe5dO8iFk6bHDyS5RcF-Ib5lvTcD3wEEWaBoV8Ii5MhiIQDke6-IsZtyG33i2q6pfvnm0tBjM0HYfPAyAaILneyuj3AGqt3t_U4hi0JbWzUvD3rCJvG2biKKpxLKHuMiQAyVN_2HI2xTT6t5XOm5domoUNLck-8kQgAhI1FReEqSCQ98ZxVD_LNTWodyYcuon-S4m")'></div>
                <div>
                    <h3 class="text-text-light dark:text-white font-bold">Rajat Sonar</h3>
                    <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Phelix Creatives</p>
                </div>
            </div>
            <p class="text-text-light/90 dark:text-white/90">"NEXtap has revolutionized how I network. It's so simple and effective. A must-have for any professional!"</p>
        </div>
        <div class="flex flex-col gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light/50 dark:bg-[#211c27]/50 backdrop-blur-sm p-6 fade-in-up stagger-delay-2 theme-transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6ojHXY3sFjz2UGeJVD_NEA53yPxXdtqA6cVHkWEpeODGc5s7nO1EoZRbX8vwdkYYhUf6uUBIxe5dO8iFk6bHDyS5RcF-Ib5lvTcD3wEEWaBoV8Ii5MhiIQDke6-IsZtyG33i2q6pfvnm0tBjM0HYfPAyAaILneyuj3AGqt3t_U4hi0JbWzUvD3rCJvG2biKKpxLKHuMiQAyVN_2HI2xTT6t5XOm5domoUNLck-8kQgAhI1FReEqSCQ98ZxVD_LNTWodyYcuon-S4m")'></div>
                <div>
                    <h3 class="text-text-light dark:text-white font-bold">Jane Doe</h3>
                    <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Designer</p>
                </div>
            </div>
            <p class="text-text-light/90 dark:text-white/90">"The website builder is incredibly intuitive. I had my portfolio up and running in under an hour. Highly recommend!"</p>
        </div>
        <div class="flex flex-col gap-4 rounded-xl border border-border-light dark:border-[#473b54] bg-card-light/50 dark:bg-[#211c27]/50 backdrop-blur-sm p-6 fade-in-up stagger-delay-3 theme-transition">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6ojHXY3sFjz2UGeJVD_NEA53yPxXdtqA6cVHkWEpeODGc5s7nO1EoZRbX8vwdkYYhUf6uUBIxe5dO8iFk6bHDyS5RcF-Ib5lvTcD3wEEWaBoV8Ii5MhiIQDke6-IsZtyG33i2q6pfvnm0tBjM0HYfPAyAaILneyuj3AGqt3t_U4hi0JbWzUvD3rCJvG2biKKpxLKHuMiQAyVN_2HI2xTT6t5XOm5domoUNLck-8kQgAhI1FReEqSCQ98ZxVD_LNTWodyYcuon-S4m")'></div>
                <div>
                    <h3 class="text-text-light dark:text-white font-bold">Mike Ross</h3>
                    <p class="text-text-muted-light dark:text-[#ab9db9] text-sm">Photographer</p>
                </div>
            </div>
            <p class="text-text-light/90 dark:text-white/90">"Managing my contacts and seeing who tapped my card through the dashboard is a game-changer for my business."</p>
        </div>
    </div>
</section>

</main>
<footer class="border-t border-solid border-border-light dark:border-b-[#302839] mt-16 py-8 px-4 theme-transition">
<div class="flex flex-col md:flex-row justify-between items-center gap-8">
<div class="flex items-center gap-4 text-text-light dark:text-white">
<div class="size-6 text-primary">
<svg fill="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
<path d="M36.7273 44C33.9891 44 31.6043 39.8386 30.3636 33.69C29.123 39.8386 26.7382 44 24 44C21.2618 44 18.877 39.8386 17.6364 33.69C16.3957 39.8386 14.0109 44 11.2727 44C7.25611 44 4 35.0457 4 24C4 12.9543 7.25611 4 11.2727 4C14.0109 4 16.3957 8.16144 17.6364 14.31C18.877 8.16144 21.2618 4 24 4C26.7382 4 29.123 8.16144 30.3636 14.31C31.6043 8.16144 33.9891 4 36.7273 4C40.7439 4 44 12.9543 44 24C44 35.0457 40.7439 44 36.7273 44Z"></path>
</svg>
</div>
<h2 class="text-text-light dark:text-white text-xl font-bold">NEXtap</h2>
</div>
<div class="flex gap-6 text-text-muted-light dark:text-white/80">
    <a class="hover:text-text-light dark:hover:text-white theme-transition" href="/minee/about.php">About</a>
    <a class="hover:text-text-light dark:hover:text-white theme-transition" href="NextTapBuilder/minee/contact.php">Contact</a>
    <a class="hover:text-text-light dark:hover:text-white theme-transition" href="NextTapBuilder/minee/FYQ.php">FAQ</a>
    <a class="hover:text-text-light dark:hover:text-white theme-transition" href="NextTapBuilder/minee/terms.php">Terms</a>
</div>
<div class="flex gap-4 text-text-muted-light dark:text-white/80">
<a class="hover:text-text-light dark:hover:text-white theme-transition" href="#">
<svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path clip-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" fill-rule="evenodd"></path></svg>
</a>
<a class="hover:text-text-light dark:hover:text-white theme-transition" href="#">
<svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
</a>
<a class="hover:text-text-light dark:hover:text-white theme-transition" href="#">
<svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12.001c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.009-.868-.014-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.03-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.338 4.695-4.566 4.942.359.308.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.001 10.001 0 0022 12.001C22 6.477 17.523 2 12 2z" fill-rule="evenodd"></path></svg>
</a>
</div>
</div>
<div class="text-center text-text-muted-light dark:text-white/50 text-sm mt-8">
    © 2024 NEXtap. All rights reserved.
</div>
</footer>
</div>
</div>
</div>
</div>

<script>
// Menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // User menu toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    const guestMenuButton = document.getElementById('guest-menu-button');
    const guestMenu = document.getElementById('guest-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('show');
            userMenu.classList.toggle('hidden');
            if (guestMenu) guestMenu.classList.add('hidden');
        });
    }
    
    if (guestMenuButton && guestMenu) {
        guestMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            guestMenu.classList.toggle('show');
            guestMenu.classList.toggle('hidden');
            if (userMenu) userMenu.classList.add('hidden');
        });
    }
    
    // Close menus when clicking outside
    document.addEventListener('click', function() {
        if (userMenu) {
            userMenu.classList.add('hidden');
            userMenu.classList.remove('show');
        }
        if (guestMenu) {
            guestMenu.classList.add('hidden');
            guestMenu.classList.remove('show');
        }
    });
    
    // Theme toggle functionality
    const themeToggleMenu = document.getElementById('theme-toggle-menu');
    const themeIcon = document.querySelector('.theme-icon');
    const themeText = document.querySelector('.theme-text');
    
    // Check for saved theme preference or respect OS preference
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme');
    
    function updateThemeUI() {
        if (document.documentElement.classList.contains('dark')) {
            if (themeIcon) themeIcon.textContent = 'light_mode';
            if (themeText) themeText.textContent = 'Light Mode';
        } else {
            if (themeIcon) themeIcon.textContent = 'dark_mode';
            if (themeText) themeText.textContent = 'Dark Mode';
        }
    }
    
    if (currentTheme === 'dark' || (!currentTheme && prefersDarkScheme.matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    updateThemeUI();
    
    // Theme toggle menu click handler
    if (themeToggleMenu) {
        themeToggleMenu.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeUI();
            
            // Close the menu after selection
            if (userMenu) {
                userMenu.classList.add('hidden');
                userMenu.classList.remove('show');
            }
            if (guestMenu) {
                guestMenu.classList.add('hidden');
                guestMenu.classList.remove('show');
            }
        });
    }

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all animated elements
    document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
        observer.observe(el);
    });

    // Enhanced count-up animation with easing
    const counter = document.getElementById('cards-counter');
    const target = <?php echo $cards_sold; ?>;
    const duration = 2500;
    let startTime = null;

    function animateCount(currentTime) {
        if (!startTime) startTime = currentTime;
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const currentCount = Math.floor(easeOutQuart * target);
        
        counter.textContent = currentCount.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(animateCount);
        } else {
            counter.textContent = target.toLocaleString();
        }
    }

    // Start count animation when section comes into view
    const cardsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                requestAnimationFrame(animateCount);
                cardsObserver.unobserve(entry.target);
            }
        });
    });

    cardsObserver.observe(document.getElementById('cards-sold'));

    // Add hover effects to cards
    const cards = document.querySelectorAll('.flex-col.rounded-xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
</body>
</html>