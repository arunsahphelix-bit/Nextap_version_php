<?php
require_once 'config.php';
require_once 'includes/db.php';

$message_sent = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = "All fields are required!";
    } else {
        try {
            // Insert into contact_messages with status as 'new'
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message, 'new']); // Use 'new' instead of 'pending'
            $message_sent = true;
        } catch (PDOException $e) {
            $error = "Failed to send message: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Contact Us - NEXtap</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&amp;display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "primary": "#7f13ec",
                "background-light": "#f7f6f8",
                "background-dark": "#000000",
            },
            fontFamily: {
                "display": ["Inter", "sans-serif"]
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
.material-symbols-outlined {
    font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 24
}
</style>
</head>
<body class="bg-background-dark font-display text-white">

<div class="max-w-6xl mx-auto px-4 md:px-10 lg:px-20 py-10 md:py-20">
<div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16">

<!-- Contact Form Section -->
<div class="lg:col-span-3 bg-[#111111] p-8 rounded-xl border border-[#333]">
<h2 class="text-3xl font-bold mb-6 text-white">Get in Touch</h2>

<?php if ($message_sent): ?>
    <div class="bg-green-600 p-4 rounded mb-6">Thank you! Your message has been sent successfully.</div>
<?php elseif ($error): ?>
    <div class="bg-red-600 p-4 rounded mb-6"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" class="space-y-6">
    <div class="flex flex-col sm:flex-row gap-6">
        <label class="flex flex-col min-w-40 flex-1">
            <p class="text-white text-base font-medium leading-normal pb-2">Full Name</p>
            <input type="text" name="name" placeholder="Enter your full name" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary/50 border border-[#444] bg-[#1a1a1a] h-14 p-[15px] text-base font-normal leading-normal" required>
        </label>
        <label class="flex flex-col min-w-40 flex-1">
            <p class="text-white text-base font-medium leading-normal pb-2">Email Address</p>
            <input type="email" name="email" placeholder="Enter your email address" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary/50 border border-[#444] bg-[#1a1a1a] h-14 p-[15px] text-base font-normal leading-normal" required>
        </label>
    </div>

    <label class="flex flex-col min-w-40 flex-1">
        <p class="text-white text-base font-medium leading-normal pb-2">Subject</p>
        <input type="text" name="subject" placeholder="Enter the subject of your message" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary/50 border border-[#444] bg-[#1a1a1a] h-14 p-[15px] text-base font-normal leading-normal" required>
    </label>

    <label class="flex flex-col min-w-40 flex-1">
        <p class="text-white text-base font-medium leading-normal pb-2">Your Message</p>
        <textarea name="message" rows="6" placeholder="Type your message here..." class="form-textarea flex w-full min-w-0 flex-1 resize-y overflow-hidden rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary/50 border border-[#444] bg-[#1a1a1a] h-36 p-[15px] text-base font-normal leading-normal" required></textarea>
    </label>

    <button type="submit" class="w-full flex items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
        <span class="truncate">Send Message</span>
    </button>
</form>
</div>

<!-- Contact Info Section -->
<div class="lg:col-span-2 space-y-8">
<div class="bg-[#111111] p-8 rounded-xl border border-[#333]">
<h3 class="text-xl font-bold mb-4 text-white">Contact Information</h3>
<div class="space-y-4">
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">mail</span>
<div>
<p class="font-medium text-white">Email</p>
<a class="text-[#a196b1] hover:text-primary transition-colors" href="mailto:support@nextap.com">support@nextap.com</a>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">call</span>
<div>
<p class="font-medium text-white">Phone</p>
<a class="text-[#a196b1] hover:text-primary transition-colors" href="tel:+11234567890">+1 (123) 456-7890</a>
</div>
</div>
<div class="flex items-start gap-4">
<span class="material-symbols-outlined text-primary mt-1">location_on</span>
<div>
<p class="font-medium text-white">Address</p>
<p class="text-[#a196b1]">Phelix Creatives, 123 Innovation Drive, Tech City, 54321</p>
</div>
</div>
</div>
</div>
<div class="bg-[#111111] p-8 rounded-xl border border-[#333]">
<div class="w-full h-64 rounded-lg overflow-hidden">
<img class="w-full h-full object-cover" data-alt="A stylized map of a city with pins indicating locations." data-location="Tech City" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD18XEFhz5QCouhTWSCCWWdtBYZt_WFc6TsYXCfy1HChwODas8RuMIwmJM7YQUzft98Kn2zwEZqAg86B8n5mjvbk0KdqojdTPiWf3sxKQ6HUnP4Y2YXLkq1_i-VTW7BNMoMjPIoMBytJ6Zv2s1nLaxXjPoIbPklsrTpJqjQiX3GQ0z22nGVzxOcleBn-K1jFQZF8VROC0GVu7rxnokcfYtkC3-Ebb1_eUJHowj-5wNgKcVx5dP01x16fLuc0-t7BNLoMyKf9Qz09Hqu"/>
</div>
</div>
</div>

</div>
</div>

</body>
</html>
