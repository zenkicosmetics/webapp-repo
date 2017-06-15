<script>
    $(function() {
        var addressTable = $('#addresses').DataTable({
            ordering: false,
            paging: false,
            info: false
        });

        $('#keyboard').keyboard({
            layout: 'custom',
            customLayout: {
                'default': [
                    'A B C D E F',
                    'G H I J K L',
                    'M N O P Q R',
                    'S T U V W X',
                    'Y Z Ä Ö Ü ß',
                    '',
                    '1 2 3 4 5',
                    '6 7 8 9 0',
                    '',
                    '. {space} {clear} {b}'
                ]
            },
            display: {
                'clear': '\u2716:Clear'
            },
            alwaysOpen: true,
            stayOpen: true,
            preventPaste: true,
            accepted: function(e, keyboard, el) {
                alert($(this).val());
            },
            change: function(e, keyboard, el) {
                var search = keyboard.$preview.val();
                addressTable.search(search).draw();
            }
        });
    });
</script>
