<?php
    function getTasks($db) {
        $sql = "SELECT * FROM task";
        $result = $db->query($sql);
        foreach ($result as $task) {
            $id = $task["id"];
            $array[$id]["title"] = $task['title'];
        }
        if($array) {
            echo json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        } else {
            echo "データがありません。" . PHP_EOL;
        }
    }
    function createTask($db) {
        if(!empty($_POST)) {
            $title = $_POST['title'];

            try {
                $sql = "INSERT INTO task (title) VALUES ('$title')";
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
    function getTask($db,$id) {
        $sql = "SELECT * FROM task WHERE id=$id";
        $result = $db->query($sql);
        if($result) {
            foreach ($result as $task) {
                $id = $task["id"];
                $array[$id]["title"] = $task['title'];
                echo json_encode($array, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            }
        } else {
            echo "データがありません。" . PHP_EOL;
        } 
    }

    function updateTask($db,$id) {
        parse_str(file_get_contents('php://input'), $put_param);
        if(!empty($put_param))
        {
            $title = $put_param['title'];
            try {
                $sql = "UPDATE task SET title='$title' WHERE id=$id";
                $result = $db->query($sql);
                echo '変更しました' . PHP_EOL;
            } catch (PDOException $e) {
                echo "変更できませんでした" . PHP_EOL;
                echo $e->getMessage();
                exit;
            }
        }
    }

    function deleteTask($db,$id) {
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