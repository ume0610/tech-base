<!DOCTYPE html>
<html lang="ja">
 <head>
  <title>mission_5-1</title>
  <meta charset="utf-8"/>
 </head>
    
    <?php
    //接続
    $dsn = 'mysql:dbname=tb230020db;host=localhost';
    $user = 'tb-230020';
    $password = 'Jp8ydsRnUc';
    $sql = "CREATE TABLE IF NOT EXISTS tbtk"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "password TEXT,"
        . "date TEXT"
        .");";
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $stmt =$pdo->query($sql);
    $message=0;
    $error=0;

    //送信1
    if(isset ($_POST["sm_1"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $password=$_POST["password"];
        $date=date("Y/m/d/H/i/s");
        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["post_number"])){
            //投稿番号があるとき、編集
            $id = $_POST["post_number"]; //変更する投稿番号
            $sql = 'UPDATE tbtk SET name=:name,comment=:comment,password=:password,date=:date WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->execute();
            $message=1; 
        }
        elseif(!empty($_POST["name"]) && !empty($_POST["comment"])){
            //投稿番号がないとき、新規保存
            $sql = $pdo -> prepare("INSERT INTO tbtk (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> execute();
            $message=4;
        }elseif(empty($_POST["name"]) || empty($_POST["comment"])){
            //名前とコメントのいすれかが入力されていないとき
           $error=1;
        }
        
    }
    //送信2
    elseif(isset($_POST["sm_2"])){
        if(!empty($_POST["del"]) && !empty($_POST["password_del"])){
            //idとパスワードが一致するデータをさがす
            $del=$_POST["del"];
            $password_del=$_POST["password_del"];
            $sql = 'SELECT id FROM tbtk where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $del, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetch();
            if(!empty($results)){
                //フォーム表示
                $sql = 'DELETE FROM tbtk WHERE id=:id AND password=:password';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $del, PDO::PARAM_INT);
                $stmt->bindParam(':password', $password_del, PDO::PARAM_STR);
                $stmt->execute();
                $message=2;    
            }else{
                //一致しない
                $error=2;
            }
        }
    }
    //送信3
    elseif(isset($_POST["sm_3"])){
        if(!empty($_POST["edit"]) && !empty($_POST["password_edit"])){
            //idとパスワードが一致するデータをさがす
            $edit=$_POST["edit"];
            $password_edit=$_POST["password_edit"];
            $sql = 'SELECT * FROM tbtk where id=:id AND password=:password' ;
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
            $stmt->bindParam(':password', $password_edit, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetch();
            if(!empty($results)){
                //フォーム表示
                $edit_number=$results['id'];
                $edit_name=$results['name'];
                $edit_comment=$results['comment'];
                $message=3;
            }else{
                //一致しない
                $edit_number=null;
                $edit_name=null;
                $edit_comment=null; 
                $error=3;
            } 
        }
    }
    ?>
    <body>
     <form action="" method="post">
         <!--新規投稿フォーム-->
         <h3>名前入力欄</h3>
         <input type="text" name="name" placeholder="氏名" value="<?php if(isset($_POST["sm_3"])){echo $edit_name;} ?>">
         <h3>コメント入力欄</h3>
         <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($_POST["sm_3"])){echo $edit_comment;}?>">
         <h3>パスワード入力欄</h3>
         <h5> <?php if($error==1) {echo "いずれの項目も正しく入力してください<br>";} elseif($message==1){echo "変更内容が保存されました。<br>";}elseif($message==4){"送信内容が保存されました。<br>";}?></h5>
         <input type="text" name="password" placeholder="パスワード">
         <input type="submit" name="sm_1"><br>
        　<!--投稿番号-->
         <input type="hidden" name="post_number" value="<?php if(isset($_POST["sm_3"])){echo $edit_number;} ?>">
         <!--削除フォーム-->
         <h3>削除番号入力欄</h3>
         <input type="number" name="del" placeholder="削除する番号">
         <h3>パスワード入力欄</h3>
         <input type="text" name="password_del" placeholder="パスワード">
         <h5><?php if($error==2) {echo "いずれの項目も正しく入力してください<br>";} elseif($message==2){echo "選択した投稿が削除されました。<br>";}?></h5>
         <input type="submit" name="sm_2" value="削除">
         <!--編集フォーム-->
         <h3>編集番号</h3>
         <input type="number" name="edit" placeholder="編集する番号">
         <h3>パスワード入力欄</h3>
         <input type="text" name="password_edit" placeholder="パスワード">
         <h5><?php if($error==3) {echo "いずれの項目も正しく入力してください<br>";} elseif($message==3){echo "選択した投稿を表示しました。<br>";}?></h5>
         <input type="submit" name="sm_3" value="編集">
     </form>
    </body>
    <?php
    //表示
    $sql = 'SELECT * FROM tbtk';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
        echo "<hr>";
    }
    ?>
 </html>