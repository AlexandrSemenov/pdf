<?php
ini_set('display_errors', 1);

require_once "../lib/php/vendor/autoload.php";
require_once "db.php";
require_once"../lib/php/PdfRotate.php";
require_once"../lib/php/PdfDelete.php";
require_once "../lib/php/vendor/propa/tcpdi/tcpdi.php";

$app = new \Slim\Slim();


$app->get('/', function () {
    $db = new DB();
    $results = $db->fpdo->from('files')->select('name', 'file_path');


    echo "<table>";
    foreach($results as $result)
    {
        $pdf = new TCPDI();
        $count = $pdf->setSourceFile($_SERVER['DOCUMENT_ROOT'] . $result['file_path']);

        $response = "";

        $response .= "<tr>";
        $response .= "<td>" . $result['id'] . "</td>";
        $response .= "<td>" . $result['name'] . "</td>";
        $response .= "<td><div class='view' data-type='view' data-path='" . $result['file_path'] . "' onClick='viewModal(this)'>Просмотреть</div></td>";
        $response .= "<td><div class='edit' data-type='edit' data-path='" . $result['file_path'] . "' data-count='" . $count . "' onClick='viewModal(this)'>Редактировать</div></td>";
        $response .= "<td><div class='delete' data-id='" . $result['id'] . "' onClick='deletePdf(this)'>Удалить</div></td>";
        $response .= "</tr>";

        print $response;
    }
    echo "<script>
            function viewModal(obj){
                $('.modal-wrapp, .modal-' + $(obj).attr('data-type')).css('display', 'block');
                $('.modal-' + $(obj).attr('data-type') + ' embed').attr('src', $(obj).attr('data-path'));

                if($(obj).attr('data-type') == 'edit'){
                    var pages = $(obj).attr('data-count');

                    for(var i = 1   ; i <= pages; i++ ){
                        $('select').append('<option value='+ i +'>' + i +'</option>');
                    }
                    $('#path').attr('value', $(obj).attr('data-path'));
                }
            };
          </script>
          ";

    echo "<script>
            function deletePdf(obj){
                var id = $(obj).attr('data-id');
                $.ajax({
                    method: 'POST',
                    url: 'api/delete/' + id,
                    success: function(){
                        alert ('Pdf файл удален');
                        refresh();
                    }
                });
            };

            function refresh()
                {
                    $.ajax({
                        method: 'GET',
                        url: 'api/',
                        success:function(html)
                        {
                            var result = $(html);
                            $('#table tbody').html(result.find('tbody').html());
                        }
                    });
                }
          </script>";
});

$app->get('/post', function () {
    echo "Post page";
});

$app->post('/upload', function(){
    if(!empty($_FILES["file"])){

        $file_name = $_FILES['file']['name'];

        $upload_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        $upload_file_path = $upload_path . $file_name;

        move_uploaded_file($_FILES['file']['tmp_name'], $upload_file_path);

        $db = new DB();
        $values = array('name' => $file_name, 'file_path' => '/uploads/' . $file_name);
        $db->fpdo->insertInto('files', $values)->execute();

        echo $file_name;
    } else {
        echo "Файл не получен";
    }
});


$app->post('/delete/:id', function($id){
    $db = new DB();
    $db->fpdo->deleteFrom('files', $id)->execute();
});


$app->post('/rotate', function(){
    $path = $_POST['pdf_path'];
    $page = $_POST['page'];

    $file = $_SERVER['DOCUMENT_ROOT'] . $path;

    $pdfRotate = new PdfRotate();
    $pdfRotate->rotatePDF($file, 90, $page);
});

$app->post('/delete-page', function(){
    $pdfDelete = new PdfDelete();
    $pdfDelete->deletePdf($_SERVER['DOCUMENT_ROOT'] . '/uploads/pdf-sample3.pdf', 2);
});

$app->run();