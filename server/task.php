<?php
    function getTasks($db) {
        $sql = "SELECT * FROM task";
        $result = $db->query($sql);
        if($result) {
            foreach ($result as $task) {
                $id = $task["id"];
                $array[$id]["title"] = $task['title'];
                // $array[$id]["description"] = $task['description'];
            }
            echo json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo "データがありません。" . PHP_EOL;
        }
    }
    function createTask($db) {
        if(!empty($_POST)) {
            $title = $_POST['title'];
            // $description = $_POST['description'];

            try {
                $sql = "INSERT INTO task (title) VALUES ('$title')";
                // $sql = "INSERT INTO task (title, description) VALUES ('$title', '$description')";
                $result = $db->query($sql);

                if ($result) {
                    unset($_POST);
                    echo "作成しました。" . PHP_EOL;
                } else {
                    echo "作成できませんでした" . PHP_EOL;
                }
            } catch(PDOException $e){
               echo $e->getMessage();
            }
        }
    }
    function getTask($db,$id)
    {
        $sql = "SELECT * FROM task WHERE id=$id";
        $result = $db->query($sql);
        if($result) {
            foreach ($result as $task) {
                $id = $task["id"];
                $array[$id]["title"] = $task['title'];
                // $array[$id]["description"] = $task['description'];
                echo json_encode($array, JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo "データがありません。" . PHP_EOL;
        }
    }

    function updateTask($db,$id)
    {
        parse_str(file_get_contents('php://input'), $put_param);
        if(!empty($put_param))
        {
            $title = $put_param['title'];
            // $description = $put_param['description'];
            try {
                $sql = "UPDATE task SET title='$title' WHERE id=$id";
                // $sql = "UPDATE task SET title='$title', description='$description' WHERE id=$id";
                $result = $db->query($sql);
                echo '変更しました' . PHP_EOL;
            } catch (PDOException $e) {
                echo "変更できませんでした" . PHP_EOL;
                echo $e->getMessage();
                exit;
            }
        }
    }

    function deleteTask($db,$id)
    {
        if(!empty($id)) {
            $sql = "DELETE FROM task WHERE id=$id";
            $result = $db->query($sql);
            echo '消去できました。' . PHP_EOL;
        } else {
            echo "消去できませんでした。" . PHP_EOL;
            exit;
        }
    }
?>