<div class="container-fluid text-center mt-5">
    <div class="error mx-auto" data-text="500">500</div>
    <p class="lead text-gray-800 mb-4"><?php echo htmlspecialchars($message); ?></p>
    <p class="text-gray-500 mb-0">Something went wrong on our end. Please try again later.</p>
    <a href="/dashboard" class="btn btn-primary mt-3">&larr; Back to Dashboard</a>

    <style>
        .error {
            color: #5a5c69;
            font-size: 7rem;
            position: relative;
            line-height: 1;
            width: 12.5rem;
            margin: 0 auto;
        }
        .error:before {
            content: attr(data-text);
            position: absolute;
            left: -2px;
            text-shadow: 1px 0 #e74a3b;
            top: 0;
            color: #5a5c69;
            background: #f8f9fc;
            overflow: hidden;
            clip: rect(0, 900px, 0, 0);
            animation: noise-anim-2 3s infinite linear alternate-reverse;
        }
        .error:after {
            content: attr(data-text);
            position: absolute;
            left: 2px;
            text-shadow: -1px 0 #4e73df;
            top: 0;
            color: #5a5c69;
            background: #f8f9fc;
            overflow: hidden;
            clip: rect(0, 900px, 0, 0);
            animation: noise-anim 2s infinite linear alternate-reverse;
        }
        @keyframes noise-anim {
            0% { clip: rect(49px, 9999px, 40px, 0); }
            5% { clip: rect(75px, 9999px, 72px, 0); }
            10% { clip: rect(97px, 9999px, 93px, 0); }
            /* Additional keyframes trimmed for brevity */
            100% { clip: rect(67px, 9999px, 78px, 0); }
        }
        @keyframes noise-anim-2 {
            0% { clip: rect(16px, 9999px, 10px, 0); }
            5% { clip: rect(75px, 9999px, 67px, 0); }
            10% { clip: rect(48px, 9999px, 27px, 0); }
            /* Additional keyframes trimmed for brevity */
            100% { clip: rect(91px, 9999px, 88px, 0); }
        }
    </style>
</div>