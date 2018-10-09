jQuery(document).ready( function() {

    function getInputValue(trObj, tdIndex, fieldType, fieldIndex) {
        fieldIndex = typeof fieldIndex !== 'undefined' ? fieldIndex : 0;
        var fieldObj = trObj.find('td').eq(tdIndex).find(fieldType)[fieldIndex];
        if(fieldType === 'input[type="checkbox"]'){

            return fieldObj.checked ? 1:0;

        }
        return fieldObj.value;
    }

    function getNewRow(event, index, advanced) {
        advanced = typeof advanced !== 'undefined' ? advanced : 0;

        var html = "<tr>"; //start row
        switch (event) {
            case "click":
                html+="<td><input type='text' value=''/></td>";
                html+="<td><select>\n\
                        <option selected value='id'>id</option>\n\
                        <option value='class'>class</option>";
                if(advanced){
                    html+="<option value='advanced'>advanced</option>";
                }
                html+="</select></td>";
                html+="<td><input type='text' value=''/></td>\n\
                        <td><input type='text' value=''/></td>\n\
                        <td><input type='text' value=''/></td>\n\
                        <td><input type='number' value=''/></td>\n\
                        <td><select id='click'>\n\
                            <option selected value='true'>true</option>\n\
                            <option value='false'>false</option>\n\
                        </select></td>";
                break;
            case "divs":
                html+="<td><input type='text' value=''/></td>";
                html+="<td><select>\n\
                        <option selected value='id'>id</option>\n\
                        <option value='class'>class</option>";
                if(advanced){
                    html+="<option value='advanced'>advanced</option>";
                }
                html+="</select></td>";

                html+="<td><input type='text' value=''/></td>\n\
                        <td><input type='text' value=''/></td>\n\
                        <td><input type='text' value=''/></td>\n\
                        <td><input type='number' value=''/></td>\n\
                        <td><select id='divs'>\n\
                            <option selected value='true'>true</option>\n\
                            <option value='false'>false</option>\n\
                        </select></td>";
                break;

            default:
                break;
        }


        html+="<td><a class='btn-add' href='#'>Add</a></td></tr>"; //end row

        return html;
    }


    function getEventData(tr, event) {

        var data = {};

        switch (event) {
            case "click":
                data['name'] = getInputValue(tr, 0, 'input');
                data['type'] = getInputValue(tr, 1, 'select');
                data['category'] = getInputValue(tr, 2, 'input');
                data['action'] = getInputValue(tr, 3, 'input');
                data['label'] = getInputValue(tr, 4, 'input');
                data['value'] = getInputValue(tr, 5, 'input');
                data['interaction'] = getInputValue(tr, 6, 'select');
                break;

            case "divs":
                data['name'] = getInputValue(tr, 0, 'input');
                data['type'] = getInputValue(tr, 1, 'select');
                data['category'] = getInputValue(tr, 2, 'input');
                data['action'] = getInputValue(tr, 3, 'input');
                data['label'] = getInputValue(tr, 4, 'input');
                data['value'] = getInputValue(tr, 5, 'input');
                data['interaction'] = getInputValue(tr, 6, 'select');
                break;
            default:
                break;
        }
        return JSON.stringify(data);
    }


    jQuery(document).on('click', '.btn-remove', function (event) {
        event.preventDefault();
        var button = jQuery(this);
        var tr = button.closest('tr');
        var tbody = tr.closest('tbody');
        var table = tbody.closest('table');
        var tblName = table.data('name');

        var index = tr.index();

        var input = getEventData(tr, tblName);


        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: wpgae_ajax.ajaxurl,
            data: {
                action: 'remove_wpgae_event',
                security: wpgae_ajax.ajaxnonce,
                event: tblName,
                index: index
            },
            success: function (response) {
                if (response.success) {
                    tr.remove();
                }
            }
        });
    });

    jQuery(document).on('click', '.btn-add', function (event) {

        event.preventDefault();
        var button = jQuery(this);
        var tr = button.closest('tr');
        var tbody = tr.closest('tbody');
        var table = tbody.closest('table');
        var tblName = table.data('name');
        var index = tr.index();
        var input = getEventData(tr, tblName);

        var newRow = getNewRow(tblName, index, wpgae_ajax.advanced);
        var removeBtn = "<td><a class='btn-remove' href='#'><i class='fa fa-times' title='Remove' aria-hidden='true'></i></a></td>";

        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: wpgae_ajax.ajaxurl,
            data: {
                action: 'add_wpgae_event',
                security: wpgae_ajax.ajaxnonce,
                event: tblName,
                input: input
            },
            success: function (response) {
                if (response.success) {
                    tbody.append(newRow);
                    button.closest('td').after(removeBtn);
                    button.html("<i class='fa fa-floppy-o' title='Update' aria-hidden='true'></i>");
                    button.attr('class', 'btn-update');
                }
            }
        });
    });

    jQuery(document).on('click', '.btn-update', function (event) {
        event.preventDefault();
        var button = jQuery(this);
        var tr = button.closest('tr');
        var tbody = tr.closest('tbody');
        var table = tbody.closest('table');
        var tblName = table.data('name');

        var index = tr.index();

        var input = getEventData(tr, tblName);
        jQuery(this).hide();
        var self = this;
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: wpgae_ajax.ajaxurl,
            data: {
                action: 'update_wpgae_event',
                security: wpgae_ajax.ajaxnonce,
                event: tblName,
                index: index,
                input: input
            },
            success: function (response) {
                jQuery(self).show();
            }
        });
    });
})