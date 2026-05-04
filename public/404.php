<?php
// 404 - Page Not Found Error Page
// No headers included to avoid errors during error pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { --color-primary: #4F46E5; --color-primary-hover: #4338CA; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-md w-full">
            <!-- Error Icon -->
            <div class="text-6xl mb-6">🔍</div>
            
            <!-- Error Code -->
            <h1 class="text-5xl sm:text-6xl font-bold text-gray-900 mb-4">404</h1>
            
            <!-- Error Title -->
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">
                Page Not Found
            </h2>
            
            <!-- Error Description -->
            <p class="text-gray-600 mb-8 text-sm sm:text-base">
                Sorry, we couldn't find the page you're looking for. It might have been moved or deleted.
            </p>
            
            <!-- Action Buttons -->
            <div class="space-y-3 sm:space-y-0 sm:space-x-3">
                <a 
                    href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'index.php'; ?>"
                    class="inline-block px-6 sm:px-8 py-3 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-900 transition min-h-[44px] flex items-center justify-center"
                >
                    ← Go Back
                </a>
                <a 
                    href="index.php"
                    class="inline-block px-6 sm:px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition min-h-[44px] flex items-center justify-center"
                >
                    Go to Home
                </a>
            </div>
            
            <!-- Help Text -->
            <p class="text-gray-500 text-xs sm:text-sm mt-8">
                Error Code: 404 | If you believe this is a mistake, please contact support.
            </p>
        </div>
    </div>
</body>
</html>
