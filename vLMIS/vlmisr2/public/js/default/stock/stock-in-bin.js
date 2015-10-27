$(function() {//
    $(".transfer-stock").click(function() {
        $.ajax({
            type: "POST",
            url: appName + "/stock/transfer-stock",
            data: {placement_id: $(this).attr('editid'), bin_id: $("#bin_id").val(),quantity_per_pack: $("#quantity_per_pack").val(),totqty: $("#totqty").val(),area: $("#area").val(),level: $("#level").val()},
            
            //data: {barcode_type_id: bar_id, number: $('#item_pack_size_id').val()},
            dataType: 'html',
            success: function(data) {
                $('#modal-body-contents').html(data);
                $('#transfer-stock').show();
            }
        });
    });

    // GRID Sorting Start
    $('th.sorting, th.sorting_asc, th.sorting_desc').click(function(e) {
        e.preventDefault();

        var self = $(this);

        var order = '';
        var sort = '';
        var counter = '';
        var page = '';
        var id = $("#bin_id").val();

        order = self.data('order');
        sort = self.data('sort');

        counter = $('#records').val();
        page = $('#current').val();

        document.location = appName + '/stock/stock-in-bin/?order=' + order + '&sort=' + sort + '&counter=' + counter + '&page=' + page + '&id=' + id;
    });
    // GRID Sorting End

    // GRID Counter Start
    $('#records').change(function(e) {
        e.preventDefault();

        var counter = $(this).val();
        var page = $('#current').val();
        var id = $("#bin_id").val();

        document.location = appName + '/stock/stock-in-bin/?counter=' + counter + '&page=' + page + '&id=' + id;
    });
    // GRID Counter End
});




