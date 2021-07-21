function appendColumnCard(col, item){
    html = '';
    html += '<div class="crm-kanban-row" data-company-id="' + item.ID + '">';
    html += '<div class="crm-kanban-card">';
    html += '<p><a href="/crm/company/details/' + item.ID + '/">' + item.TITLE + '</a></p>';
    //html += '<p>' + item.DATE_MODIFY + '</p>';
    html += '<p>Город: ' + item.UF_CRM_1519584932 + '</p>';
    html += '<p>Оборот: ' + item.REVENUE + '</p>';
    html += '<p>Форма: ' + item.UF_CRM_1535965570988 + '</p>';
    html += '</div>';
    html += '</div>';
    col.append(html);
}

$(function() {

    $( "[data-column]" ).sortable({
        connectWith: "div",
        placeholder: "crm-kanban-card-placeholder",
        update: function(event, ui) {
            //console.log('Change');
            if(ui.sender){
                $grid = $(event.target).closest('[data-columns-ufname]');
                BX.ajax.runComponentAction(
                    'prominado:crm.company.kanban',
                    'changeColumn',
                    {
                        mode: 'class',
                        data: {
                            post: {
                                colUfName: $grid.data('columns-ufname'),
                                companyId: $(ui.item).data('company-id'),
                                columnId: $(event.target).data('column-id'),
                            }
                        },
                    }
                ).then(function(response) {
                        if (response.status === 'success') {
                            //console.log(response.data);
                        }else{
                            console.log('Error while change company');
                        }
                    }
                );
            }
        }
    });

    $('.crm-kanban-column-items').on('scroll', function(){
        $col= $(this);
        scrollTop = $col.scrollTop();
        scrollMax = Math.round($col[0].scrollHeight) - Math.round($col.height());
        if(scrollTop >= scrollMax){
            //console.log('Bottom');
            $grid = $col.closest('[data-columns-ufname]');
            BX.ajax.runComponentAction(
                'prominado:crm.company.kanban',
                'getCompanies',
                {
                    mode: 'class',
                    data: {
                        post: {
                            colUfName: $grid.data('columns-ufname'),
                            colId: $col.data('column-id'),
                            colPage: $col.data('column-page') + 1
                        }
                    },
                }
            ).then(function(response) {
                    if (response.status === 'success') {
                        arItems = JSON.parse(response.data);
                        $.each(arItems, function(index, item) {
                            appendColumnCard($col, item);
                            //console.log(item);
                        });
                    }else{
                        console.log('Error while loading companies');
                    }
                }
            );
        }
    });

});
