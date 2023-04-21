<!DOCTYPE html>
<html>
<head>
    <title>Yorumlar</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        form {
            margin-top: 30px;
            max-width: 500px;
            margin-left: 0;
            margin-right: auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 150px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .comment {
            margin-top: 30px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            background-color: #f5f5f5;
        }

        .comment h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .comment p {
            margin: 0;
            margin-bottom: 10px;
        }

        .comment span {
            font-size: 12px;
            color: #999;
        }

        /* Yeni eklenen özellikler */
        .reply {
            margin-top: 20px;
            margin-bottom: 10px;
            display: none;
        }

        .reply textarea {
            height: 100px;
        }
    </style>
</head>
<body>
    <h1>Yorumlar</h1>

    <form method="post">
        <label for="name">Adınız:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">E-posta adresiniz:</label>
        <input type="email" name="email" id="email" required>

        <label for="comment">Yorumunuz:</label>
        <textarea name="comment" id="comment" required></textarea>

        <button type="submit">Yorum Gönder</button>
    </form>
</body>
</html>
</body>
</html>
    <?php
$jsonPath = realpath('comments.json');
if (basename($jsonPath) !== 'comments.json') {
    die('Invalid file path');
}

$jsonString = file_get_contents($jsonPath);
$comments = json_decode($jsonString, true);
$comments = array_reverse($comments);

$forbiddenWords = array('küfür1', 'küfür2', 'küfür3');

foreach ($comments as $comment) {
    $commentText = strtolower($comment['comment']);
    $containsForbiddenWord = false;

    foreach ($forbiddenWords as $word) {
        if (strpos($commentText, $word) !== false) {
            $containsForbiddenWord = true;
            break;
        }
    }

    if (!$containsForbiddenWord) {
        echo '<div class="comment">';
        echo '<h3>' . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . '</h3>';
        echo '<p>' . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<span>' . htmlspecialchars($comment['email'], ENT_QUOTES, 'UTF-8') . '</span>';
        echo '</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email format');
    }

    $commentText = strtolower($comment);
    $containsForbiddenWord = false;

    foreach ($forbiddenWords as $word) {
        if (strpos($commentText, $word) !== false) {
            $containsForbiddenWord = true;
            break;
        }
    }

    if (!$containsForbiddenWord) {
        $jsonPath = realpath('comments.json');
        if (basename($jsonPath) !== 'comments.json') {
            die('Invalid file path');
        }

        $jsonString = file_get_contents($jsonPath);
        $comments = json_decode($jsonString, true);
        $comments[] = array(
            'name' => $name,
            'email' => $email,
            'comment' => $comment
        );
        $jsonData = json_encode($comments);
        file_put_contents($jsonPath, $jsonData);
    }
}
?>
