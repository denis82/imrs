<table class="table datatable-phrase">
	<thead>
		<tr>
			<th>№</th>
			<th>Слово / фраза</th>
			<th>Части речи</th>
			<th>Встречаемость</th>
			<th>На сайте</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	    <? foreach ($stat as $j => $i): ?>
			<tr>
				<td><?= $j+1 ?></td>
				<td><?= $i['phrase'] ?></td>
				<td><?= TxtHelper::phraseGrammarParts( $i['gr'] ) ?></td>
		        <td><span class="text-semibold"><?= $i['total'] ?></span></td>
		        <td><span class="text-semibold"><?= $i['site'] ?></span></td>
		        <td>
		        	<a href="#" class="add2semantic" title="Добавить к семантическому ядру" data-text="<?= $i['phrase'] ?>"><i class="icon-stack-plus"></i> добавить</a>
		        </td>
			</tr>
	    <? endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
$(function(){
    $('.datatable-phrase').DataTable({
        autoWidth: false,
        searching: true,
        paging: false,
        info: false,
        columnDefs: [{ 
            orderable: false,
            targets: [ 0, 2, 5 ]
        }],
        order: [[ 3, "desc" ]],
        scrollY: 400,

        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span>Фильтр:</span> _INPUT_',
            lengthMenu: '<span>Show:</span> _MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
        },
        drawCallback: function () {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
        },
        preDrawCallback: function() {
            $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
        }
    });
});	
</script>