$('document').ready(function(){

    refresh();

    $('#form').on('submit', function(e){
        e.preventDefault();

         var fd = new FormData();
         var myFile = document.getElementById("file").files[0];
         fd.append( 'file',  myFile);
        $.ajax({
            method: 'POST',
            url: 'api/upload',
            data: fd,
            contentType: false,
            processData: false,
            success:function(html)
            {
                alert("Файл " + html + " загружен");
                refresh();
            }
        });
    });

    function refresh()
    {
        $.ajax({
            method: "GET",
            url: "api/",
            success:function(html)
            {
                var result = $(html);
                $('#table tbody').html(result.find('tbody').html());
            }
        });
    }

    $('.rotate-page').on('click', function(e){
        e.preventDefault();

        $.ajax({
            method: "POST",
            url: "api/rotate",
            data: $('#form-edit').serialize(),
            success: function(){
                alert('PDF изменен');
            }
        });
    });

    $('.delete-page').on('click', function(e){
        e.preventDefault();

        $.ajax({
            method: "POST",
            url: "api/delete-page",
            data: $('#form-edit').serialize(),
            success: function(){
                alert('PDF изменен');
            }
        });
    });

    $('.close').click(function(){
        $('.modal-wrapp, .modal-view, .modal-edit').css('display', 'none');
        $('embed').attr('src', '');
        $('select').empty();
        $('#path').attr('value', '');
    });
});