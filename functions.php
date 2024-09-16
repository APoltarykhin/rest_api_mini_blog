
<?php

function get_posts($database){

    $posts = $database->query("SELECT * FROM `posts`")->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode($posts);
}


function get_one_post($database,$postsNumber){


    $totalPosts = $database->query("SELECT COUNT(*) FROM posts")->fetchColumn();

    // Проверяем, если $postsNumber равно 0 или больше общего количества постов
    if ($postsNumber <= 0 || $postsNumber > $totalPosts){

        http_response_code(404);

        $answer = [
            "status" => "false",
            "message" => "Post not found"
        ];

        echo json_encode($answer);

    }else{

        $stmt = $database->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute(['id' => $postsNumber]);
        $posts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //$posts = $database->query("SELECT * FROM `posts` WHERE `id`='$postsNumber'")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($posts);

    }

}

function add_posts($database,$data){

    $title = $data['title'];
    $description = $data['description'];
    $text = $data['text'];

    $addpost = $database->query("INSERT INTO `posts`(`title`, `description`, `text`) VALUES ('$title','$description','$text')");
    if ($addpost->rowCount() > 0) {

        $answer = [
        'status' => 'true',
        'insert_id' => $database->lastInsertId()
         ];

        echo json_encode($answer);
    
        http_response_code(201);

    } else {

        $answer = [
        'status' => 'false',
        'insert_id' => $database->lastInsertId()
        ];

        echo json_encode($answer);

        http_response_code(501);

    }
}

/*function update_path($database,$postsNumber,$data_array){

    $id = $postsNumber;
    $title = isset($data_array['title']) ? $data_array['title'] : null; 
    $description = isset($data_array['description']) ? $data_array['description'] : null; 
    $text = isset($data_array['text']) ? $data_array['text'] : null; 


   $update_post =  $database->query("UPDATE `posts` SET `title` = '$title', `description` = '$description', `text` = '$text' WHERE `id` = $id");


   $answer = [
        'status' => 'true',
        "message" => "Post №{$postsNumber} update"
        ];

        echo json_encode($answer);

        http_response_code(200);

}*/

function update_path($database, $postsNumber, $data_array) {
    $id = $postsNumber;

    // Формируем массив с полями и их значениями для обновления
    $updateFields = [];
    if (isset($data_array['title'])) {
        $updateFields['title'] = $data_array['title'];
    }
    if (isset($data_array['description'])) {
        $updateFields['description'] = $data_array['description'];
    }
    if (isset($data_array['text'])) {
        $updateFields['text'] = $data_array['text'];
    }

    if (empty($updateFields)) {
        $answer = [
            'status' => 'false',
            'message' => "No fields to update for post №{$postsNumber}"
        ];
        http_response_code(400);
        echo json_encode($answer);
        return;
    }

    try {
        $database->beginTransaction();

        // Формируем SQL-запрос на основе переданных полей
        $updateQuery = "UPDATE posts SET ";
        $updateParams = [];
        $i = 0;
        foreach ($updateFields as $field => $value) {
            if ($i > 0) {
                $updateQuery .= ", ";
            }
            $updateQuery .= "$field = ?";
            $updateParams[] = $value;
            $i++;
        }
        $updateQuery .= " WHERE id = ?";
        $updateParams[] = $id;

        $updateStmt = $database->prepare($updateQuery);
        $updateStmt->execute($updateParams);

        if ($updateStmt->rowCount() === 0) {
            $answer = [
                'status' => 'false',
                'message' => "Post №{$postsNumber} not found"
            ];
            http_response_code(404);
        } else {
            $answer = [
                'status' => 'true',
                'message' => "Post №{$postsNumber} updated"
            ];
            http_response_code(200);
        }

        $database->commit();
    } catch (PDOException $e) {
        $database->rollBack();
        $answer = [
            'status' => 'false',
            'message' => "Error updating post №{$postsNumber}: " . $e->getMessage()
        ];
        http_response_code(500);
    }

    echo json_encode($answer);
}

function delete_post($database,$postsNumber){

    $delete = $database->query("DELETE FROM `posts` WHERE `posts`.`id` = $postsNumber");

    $answer = [
        'status' => 'true',
        'message' => "Delete post №{$postsNumber}"
    ];
    http_response_code(200);
    echo json_encode($answer);

}
