<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeedbackHub</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            padding: 20px 50px;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .auth-buttons a {
            margin-left: 20px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .hero {
            text-align: center;
            padding: 80px 20px;
            background-color: #f8f9fa;
        }
        
        .hero h2 {
            font-size: 28px;
            margin-bottom: 30px;
        }
        
        .cta-button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
        
        .features {
            display: flex;
            justify-content: space-around;
            padding: 60px 20px;
            text-align: center;
        }
        
        .feature {
            width: 30%;
        }
        
        .feature-icon {
            font-size: 30px;
            margin-bottom: 15px;
        }
        
        hr {
            width: 80%;
            margin: 40px auto;
            border: 0;
            height: 1px;
            background-color: #ddd;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">FeedbackHub</div>
        <div class="auth-buttons">
            <a href="#">Register</a>
            <a href="#">Login</a>
        </div>
    </header>
    
    <section class="hero">
        <h2>We Respect Your Honest Opinions</h2>
        <p>MGM's College of Engineering, Nanded</p>
        <a href="#" class="cta-button">Get Started</a>
    </section>
    
    <hr>
    
    <section class="features">
        <div class="feature">
            <div class="feature-icon">?</div>
            <h3>Easy to Use</h3>
            <p>Simple interface for providing feedback</p>
        </div>
        
        <div class="feature">
            <div class="feature-icon">✔️</div>
            <h3>Secure</h3>
            <p>Your data is protected</p>
        </div>
        
        <div class="feature">
            <div class="feature-icon">📈</div>
            <h3>Effective</h3>
            <p>We implement meaningful changes</p>
        </div>
    </section>
    
    <footer>
        <p>© 2025 FeedbackHub. All rights reserved.</p>
    </footer>
</body>
</html>