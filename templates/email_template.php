<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #007bff; color: white; padding: 10px; text-align: center; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.9em; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($subject) ?></h1>
        </div>
        <div class="content">
            <?= $message ?>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> PennyPlanner</p>
        </div>
    </div>
</body>
</html>
